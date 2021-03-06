<?php

namespace org\dokuwiki\translatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="org\dokuwiki\translatorBundle\Entity\LanguageStatsEntityRepository")
 * @ORM\Table(name="languageStats")
 *      indexes={@ORM\Index(name="langName_idx", columns={"language"})}
 * )
 */
class LanguageStatsEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LanguageNameEntity")
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     * @var LanguageNameEntity
     */
    private $language;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $completionPercent;

    /**
     * @ORM\ManyToOne(targetEntity="RepositoryEntity")
     * @var RepositoryEntity
     */
    private $repository;

    /**
     * @param int $completionPercent
     */
    public function setCompletionPercent($completionPercent) {
        $this->completionPercent = $completionPercent;
    }

    /**
     * @return int
     */
    public function getCompletionPercent() {
        return $this->completionPercent;
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

    /**
     * @param LanguageNameEntity $language
     */
    public function setLanguage($language) {
        $this->language = $language;
    }

    /**
     * @return LanguageNameEntity
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @param \org\dokuwiki\translatorBundle\Entity\RepositoryEntity $repository
     */
    public function setRepository($repository) {
        $this->repository = $repository;
    }

    /**
     * @return \org\dokuwiki\translatorBundle\Entity\RepositoryEntity
     */
    public function getRepository() {
        return $this->repository;
    }


}
