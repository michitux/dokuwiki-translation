<?php
namespace org\dokuwiki\translatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="org\dokuwiki\translatorBundle\Entity\TranslationUpdateEntityRepository")
 * @ORM\Table(name="translationUpdate")
 */
class TranslationUpdateEntity {

    public static $STATE_UNDONE = 'undone';
    public static $STATE_DOING = 'doing';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RepositoryEntity")
     * @var RepositoryEntity
     */
    protected $repository;

    /**
     * @ORM\Column(type="string", length=300)
     * @var string
     */
    protected $author;

    /**
     * @ORM\Column(type="string", length=300)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $updated;

    /**
     * @ORM\Column(type="string", length=300)
     * @var string
     */
    protected $state;

    /**
     * @ORM\Column(type="string", length=100)
     * @var string
     */
    protected $language;

    /**
     * @param string $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function setRepository($repository) {
        $this->repository = $repository;
    }

    public function getRepository() {
        return $this->repository;
    }

    /**
     * @param string $state
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    /**
     * @param int $updated
     */
    public function setUpdated($updated) {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language) {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }


}
