<?php

namespace org\dokuwiki\translatorBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use org\dokuwiki\translatorBundle\Services\Language\TranslationPreparer;
use org\dokuwiki\translatorBundle\Services\Language\UserTranslationValidator;
use org\dokuwiki\translatorBundle\Services\Language\UserTranslationValidatorException;
use org\dokuwiki\translatorBundle\Services\Language\UserTranslationValidatorFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use org\dokuwiki\translatorBundle\Entity\LanguageNameEntityRepository;
use org\dokuwiki\translatorBundle\Entity\RepositoryEntity;
use org\dokuwiki\translatorBundle\Entity\RepositoryEntityRepository;
use org\dokuwiki\translatorBundle\Services\Language\LocalText;
use org\dokuwiki\translatorBundle\Services\Repository\Repository;
use org\dokuwiki\translatorBundle\Services\Repository\RepositoryManager;

class TranslationController extends Controller implements InitializableController {

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function initialize(Request $request) {
        $this->entityManager = $this->getDoctrine()->getManager();
    }

    public function saveAction(Request $request) {
        if ($request->getMethod() !== 'POST') {
            return $this->redirect($this->generateUrl('dokuwiki_translator_homepage'));
        }

        $action = $request->request->get('action', array());
        if (!isset($action['save'])) {
            return $this->redirect($this->generateUrl('dokuwiki_translator_homepage'));
        }

        $data = array();
        $data['translation'] = $request->request->get('translation', null);
        $data['repositoryName'] = $request->request->get('repositoryName', '');
        $data['repositoryType'] = $request->request->get('repositoryType', '');
        if (
                $data['translation'] === null ||
                $data['repositoryName'] === '' ||
                $data['repositoryType'] === ''
            ) {
            return $this->redirect($this->generateUrl('dokuwiki_translator_homepage'));
        }

        $data['name'] = $request->request->get('name', '');
        $data['email'] = $request->request->get('email', '');
        $language = $this->getLanguage();


        $repositoryEntity = $this->getRepositoryEntityRepository()->getRepository($data['repositoryType'], $data['repositoryName']);
        $repository = $this->getRepositoryManager()->getRepository($repositoryEntity);
        $defaultTranslation = $repository->getLanguage('en');
        $previousTranslation = $repository->getLanguage($language);

        try {
            $newTranslation = $this->validateTranslation($defaultTranslation, $previousTranslation, $data['translation'], $data['name'], $data['email']);
        } catch (UserTranslationValidatorException $e) {
            return $this->translate($data['repositoryType'], $data['repositoryName'], $data['translation'], $e);
        }

        $repository->addTranslationUpdate($newTranslation, $data['name'], $data['email'], $language);

        // forward to queue status
        return $this->redirect($this->generateUrl('dokuwiki_translate_thanks'));
    }

    protected function validateTranslation($defaultTranslation, $previousTranslation, array $userTranslation, $author, $authorEmail) {
        /** @var UserTranslationValidatorFactory $validatorFactory */
        $validatorFactory = $this->get('user_translation_validator_factory');
        $validator = $validatorFactory->getInstance($defaultTranslation, $previousTranslation,
                $userTranslation, $author, $authorEmail);
        return $validator->validate();
    }

    public function translateCoreAction() {
        return $this->translate(RepositoryEntity::$TYPE_CORE, 'dokuwiki');
    }

    public function translatePluginAction($name) {
        return $this->translate(RepositoryEntity::$TYPE_PLUGIN, $name);
    }

    private function translate($type, $name, array $userTranslation = array(), UserTranslationValidatorException $e = null) {
        $language = $this->getLanguage();
        $repositoryEntity = $this->getRepositoryEntityRepository()->getRepository($type, $name);

        if ($repositoryEntity->getState() !== RepositoryEntity::$STATE_ACTIVE) {
            return $this->redirect($this->generateUrl('dokuwiki_translator_homepage'));
        }

        $data['repository'] = $repositoryEntity;
        $data['translations'] = $this->prepareLanguages($language, $repositoryEntity, $userTranslation);

        try {
            $data['targetLanguage'] = $this->getLanguageNameEntityRepository()->getLanguageByCode($language);
        } catch (NoResultException $e) {
            return $this->redirect($this->generateUrl('dokuwiki_translator_homepage'));
        }


        return $this->render('dokuwikiTranslatorBundle:Translate:translate.html.twig', $data);
    }


    /**
     * @return RepositoryManager
     */
    private function getRepositoryManager() {
        return $this->get('repository_manager');
    }

    private function prepareLanguages($language, $repositoryEntity, array $userTranslation) {
        $repositoryManager = $this->getRepositoryManager();
        $repository = $repositoryManager->getRepository($repositoryEntity);

        $defaultTranslation = $repository->getLanguage('en');

        $targetTranslation = $userTranslation;
        if (empty($userTranslation)) {
            $targetTranslation = $repository->getLanguage($language);
        }

        /** @var TranslationPreparer $translationPreparer */
        $translationPreparer = $this->get('translation_preparer');

        return $translationPreparer->prepare($defaultTranslation, $targetTranslation);
    }

    /**
     * @return LanguageNameEntityRepository
     */
    private function getLanguageNameEntityRepository() {
        return $this->entityManager->getRepository('dokuwikiTranslatorBundle:LanguageNameEntity');
    }

    public function thanksAction() {
        return $this->render('dokuwikiTranslatorBundle:Translate:thanks.html.twig');
    }

    private function getLanguage() {
        return $this->get('language_manager')->getLanguage($this->getRequest());
    }

    /**
     * @return RepositoryEntityRepository
     */
    private function getRepositoryEntityRepository() {
        return $this->entityManager->getRepository('dokuwikiTranslatorBundle:RepositoryEntity');
    }
}
