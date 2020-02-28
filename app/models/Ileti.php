<?php
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Ileti extends Eloquent
{
    use SoftDeletingTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ileti';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['konu'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * "Users" table name to use for manual queries
     *
     * @var string|null
     */
    private $usersTable = null;

    /**
     * Messages relationship
     *
     * @return HasMany
     */
    public function mesaj()
    {
        return $this->hasMany('Mesaj');
    }

    /**
     * Returns the latest message from a thread
     *
     * @return Message
     */
    public function getLatestMessageAttribute()
    {
        if($this->mesaj()->count()>0)
            return $this->mesaj()->latest()->first();

        return new Message();
    }

    /**
     * Participants relationship
     *
     * @return HasMany
     */
    public function alici()
    {
        return $this->hasMany('Alici');
    }

    /**
     * Returns the user object that created the thread
     *
     * @return mixed
     */
    public function olusturan()
    {
        if($this->mesaj()->count()>0)
        return $this->mesaj()->oldest()->first()->kullanici;
        return '';
    }

    /**
     * Returns all of the latest threads by updated_at date
     *
     * @return mixed
     */
    public static function getAllLatest()
    {
        return self::latest('updated_at');
    }

    /**
     * Returns an array of user ids that are associated with the thread
     *
     * @param null $userId
     * @return array
     */
    public function participantsUserIds($userId = null)
    {
        $users = $this->alici()->withTrashed()->lists('kullanici_id');

        if ($userId) {
            $users[] = $userId;
        }

        return $users;
    }

    /**
     * Returns threads that the user is associated with
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeForUser($query, $userId)
    {
        return $query->join('alici', 'ileti.id', '=', 'alici.ileti_id')
            ->where('alici.kullanici_id', $userId)
            ->where('alici.deleted_at', null)
            ->select('ileti.*');
    }

    /**
     * Returns threads with new messages that the user is associated with
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeForUserWithNewMessages($query, $userId)
    {
        return $query->join('alici', 'ileti.id', '=', 'alici.ileti_id')
            ->where('alici.kullanici_id', $userId)
            ->whereNull('alici.deleted_at')
            ->where(function ($query) {
                $query->where('ileti.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . 'alici.son_okuma'))
                    ->orWhereNull('alici.son_okuma');
            })
            ->select('ileti.*');
    }

    /**
     * Returns threads between given user ids
     *
     * @param $query
     * @param $alicilar
     * @return mixed
     */
    public function scopeBetween($query, array $alicilar)
    {
        $query->whereHas('alici', function ($query) use ($alicilar) {
            $query->whereIn('kullanici_id', $alicilar)
                ->groupBy('ileti_id')
                ->havingRaw('COUNT(ileti_id)='.count($alicilar));
        });
    }

    /**
     * Adds users to this thread
     *
     * @param array $alicilar list of all participants
     * @return void
     */
    public function addParticipants(array $alicilar)
    {
        if (count($alicilar)) {
            foreach ($alicilar as $user_id) {
                Alici::firstOrCreate([
                    'kullanici_id' => $user_id,
                    'ileti_id' => $this->id,
                ]);
            }
        }
    }

    /**
     * Mark a thread as read for a user
     *
     * @param integer $userId
     */
    public function markAsRead($userId)
    {
        try {
            $alici = $this->getParticipantFromUser($userId);
            $alici->son_okuma = new Carbon;
            $alici->save();
        } catch (ModelNotFoundException $e) {
            // do nothing
        }
    }

    /**
     * See if the current thread is unread by the user
     *
     * @param integer $userId
     * @return bool
     */
    public function isUnread($userId)
    {
        try {
            $alici = $this->getParticipantFromUser($userId);
            if ($this->updated_at > $alici->son_okuma) {
                return true;
            }
        } catch (ModelNotFoundException $e) {
            // do nothing
        }

        return false;
    }

    /**
     * Finds the participant record from a user id
     *
     * @param $userId
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function getParticipantFromUser($userId)
    {
        return $this->alici()->where('kullanici_id', $userId)->firstOrFail();
    }

    /**
     * Restores all participants within a thread that has a new message
     */
    public function activateAllParticipants()
    {
        $alicilar = $this->alici()->withTrashed()->get();
        foreach ($alicilar as $alici) {
            $alici->restore();
        }
    }

    /**
     * Generates a string of participant information
     *
     * @param null $userId
     * @param array $columns
     * @return string
     */
    public function participantsString($userId=null, $columns=['adi_soyadi'])
    {
        $selectString = $this->createSelectString($columns);

        $participantNames = $this->getConnection()->table($this->getUsersTable())
            ->join('alici', 'kullanici.id', '=', 'alici.kullanici_id')
            ->where('alici.ileti_id', $this->id)
            ->select($this->getConnection()->raw($selectString));

        if ($userId !== null) {
            $participantNames->where($this->getUsersTable() . '.id', '!=', $userId);
        }

        $userNames = $participantNames->lists($this->getUsersTable() . '.adi_soyadi');

        return implode(', ', $userNames);
    }

    /**
     * Checks to see if a user is a current participant of the thread
     *
     * @param $userId
     * @return bool
     */
    public function hasParticipant($userId)
    {
        $alicilar = $this->alici()->where('kullanici_id', '=', $userId);
        if ($alicilar->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Generates a select string used in participantsString()
     *
     * @param $columns
     * @return string
     */
    protected function createSelectString($columns)
    {
        $dbDriver = $this->getConnection()->getDriverName();

        switch ($dbDriver) {
            case 'pgsql':
            case 'sqlite':
                $columnString = implode(" || ' ' || " . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . ".", $columns);
                $selectString = "(" . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . "." . $columnString . ")";
                break;
            case 'sqlsrv':
                $columnString = implode(" + ' ' + " . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . ".", $columns);
                $selectString = "(" . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . "." . $columnString . ")";
                break;
            default:
                $columnString = implode(", ' ', " . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . ".", $columns);
                $selectString = "concat(" . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . "." . $columnString . ")";
        }

        return $selectString;
    }

    /**
     * Sets the "users" table name
     *
     * @param $tableName
     */
    public function setUsersTable($tableName)
    {
        $this->usersTable = $tableName;
    }

    /**
     * Returns the "users" table name to use in manual queries
     *
     * @return string
     */
    private function getUsersTable()
    {
        if ($this->usersTable !== null) {
            return $this->usersTable;
        }

        $userModel = 'Kullanici';
        return $this->usersTable = (new $userModel)->getTable();
    }
}
