<?php

namespace BiteCodes\MonologUIBundle\Model;

use Doctrine\ORM\Mapping as ORM;

class Log implements \JsonSerializable
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $channel;

    /**
     * @var integer
     */
    protected $level;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var array
     */
    protected $extra;

    /**
     * @var array
     */
    protected $getData;

    /**
     * @var array
     */
    protected $postData;

    /**
     * @var array
     */
    protected $serverData;

    public static function create(array $record)
    {
        $log = new self;

        $log
            ->setId($record['id'])
            ->setChannel($record['channel'])
            ->setLevel($record['level'])
            ->setMessage($record['message'])
            ->setContext(json_decode($record['context'], true))
            ->setDate($record['datetime'])
            ->setExtra(json_decode($record['extra'], true))
            ->setGetData(json_decode($record['http_get'], true))
            ->setPostData(json_decode($record['http_post'], true))
            ->setServerData(json_decode($record['http_server']), true);


        return $log;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @param integer $id
     *
     * @return Log
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     *
     * @return Log
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param integer $level
     *
     * @return Log
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return Log
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     *
     * @return Log
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     *
     * @return Log
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return array
     */
    public function getServerData()
    {
        return $this->serverData;
    }

    /**
     * @param array $serverData
     *
     * @return Log
     */
    public function setServerData($serverData)
    {
        $this->serverData = $serverData;

        return $this;
    }

    /**
     * @return array
     */
    public function getPostData()
    {
        return $this->postData;
    }

    /**
     * @param array $postData
     *
     * @return Log
     */
    public function setPostData($postData)
    {
        $this->postData = $postData;

        return $this;
    }

    /**
     * @return array
     */
    public function getGetData()
    {
        return $this->getData;
    }

    /**
     * @param array $getData
     *
     * @return Log
     */
    public function setGetData($getData)
    {
        $this->getData = $getData;

        return $this;
    }

    public function toArray()
    {
        $data = [];
        $refl = new \ReflectionClass($this);

        foreach ($refl->getProperties() as $prop) {
            $data[$prop->getName()] = $this->{'get' . ucfirst($prop->getName())}();
        }

        return $data;
    }

    public function jsonSerialize()
    {
        return [
            'id'         => $this->getId(),
            'channel'    => $this->getChannel(),
            'level'      => (int) $this->getLevel(),
            'message'    => $this->getMessage(),
            'date'       => $this->getDate(),
            'context'    => $this->getContext(),
            'extra'      => $this->getExtra(),
            'getData'    => $this->getGetData(),
            'postData'   => $this->getPostData(),
            'serverData' => $this->getServerData(),
        ];
    }
}
