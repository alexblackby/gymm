<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActionTokenRepository")
 * @ORM\Table(name="action_tokens")
 */
class ActionToken
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $secret;


    /**
     * @ORM\Column(type="string", length=100)
     */
    private $action;

    /**
     * @ORM\Column(type="string", length=4096)
     */
    private $params;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expires;

    /**
     * ActionToken constructor.
     * @param string $action
     * @param int $days
     */
    public function __construct(string $action, int $days = 1)
    {
        $this->action = $action;
        $this->secret = bin2hex(random_bytes(8));
        $this->setTimeToLive($days);
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param int $days
     * @return ActionToken
     */
    public function setTimeToLive(int $days): ActionToken
    {
        $this->expires = new \DateTime("+{$days} days");
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        return $this->getTokenString();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getTokenString()
    {
        if (empty($this->id)) {
            throw new \Exception("Can't get token string until object is saved and got an ID.");
        }
        return $this->secret . ':' . $this->id;
    }

    public static function parseTokenString(string $tokenString): ?array
    {
        if (!strpos($tokenString, ':')) {
            return null;
        }
        list($secret, $id) = explode(':', $tokenString);
        if (!$secret || !$id) {
            return null;
        }

        return ["secret" => $secret, "id" => $id];
    }

    public function getParams(): ?array
    {
        if (!empty($this->params)) {
            return unserialize($this->params);
        }
    }

    public function setParams(array $params): ActionToken
    {
        $this->params = serialize($params);
        return $this;
    }

    /**
     * Проверяем, соответствует ли токен параметрам, полученным от пользователя:
     * секретному хэшу, типу токена, а также не истек ли его срок жизни
     *
     * @param string $secret
     * @param string $action
     * @return bool
     */
    public function validate(string $secret, string $action): bool
    {
        if (!$this->checkSecret($secret)) {
            return false;
        }
        if (!$this->checkAction($action)) {
            return false;
        }
        if ($this->isExpired()) {
            return false;
        }
        return true;
    }


    private function checkSecret(string $secret): bool
    {
        return $this->secret == $secret;
    }


    private function isExpired($datetime = null): bool
    {
        if (!$datetime) {
            $datetime = new \DateTime("now");
        }
        return $this->expires <= $datetime;
    }


    private function checkAction(string $action): bool
    {
        return $this->action == $action;
    }
}