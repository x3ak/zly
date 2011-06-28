<?php

/**
 * SlyS
 *
 * @abstract    contains Templater_AdminController class,
 *              extending Zend_Controller_Action
 * @author      Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version     $Id: AdminController.php 1183 2011-02-07 08:38:38Z deeper $
 */
namespace Templater;
/**
 * Themes administrator panel
 */
class AdminController extends \Zend\Controller\Action
{

    /**
     * Display templater admin dashboard
     */
    public function indexAction()
    {
        
    }

    /**
     * THEMES SECTION
     */

    /**
     * Themes list action
     */
    public function themesAction()
    {
        $themesModel = new Model\Themes();
        $this->view->themes = $themesModel->getThemesPaginator(
            $this->getRequest()->getParam('page', 1),
            $this->getRequest()->getParam('perPage', 20)
        );
    }

    /**
     * Edit Theme action
     * @return null
     */
    public function editThemeAction()
    {
        $themesModel = new Model\Themes();
        $theme = $themesModel->getTheme(
                        $this->getRequest()->getParam('id'), true);

        $form = $themesModel->getThemeEditForm($theme);
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            try{
                $themesModel->saveTheme($theme, $form->getValues());
            }catch(Exception $exception) {
                $this->_helper->getHelper('FlashMessenger')
                        ->addMessage($exception->getMessage());
            }
            
            $this->_helper->getHelper('FlashMessenger')
                    ->addMessage('Theme successful saved.');
            $this->_redirect($this->_helper->url->url(array('action'=>'themes','module'=>'templater'),'admin'));
        }
        $this->view->editThemeForm = $form;
    }

    /**
     * Delete Theme action
     */
    public function deleteThemeAction()
    {
        $model = new Model\Themes();
        try {
            $result = $model->deleteTheme($this->getRequest()->getParam('id'));
            if ($result)
            $this->_helper->getHelper('FlashMessenger')
                    ->addMessage('Theme successful deleted.');
        } catch(Exception $exception) {
            $this->_helper->getHelper('FlashMessenger')
                    ->addMessage($exception->getMessage());
        }
        $this->_redirect($this->_helper->url->url(array('action'=>'themes','module'=>'templater'),'admin'));
    }

    /**
     * LAYOUTS SECTIONS
     */

    /**
     * Layouts list action
     */
    public function layoutsAction()
    {
        $tplId = $this->getRequest()->getParam('tpl', null);
        $layoutsModel = new Mode\Layouts();
        $where = array();
        if (!empty($tplId))
            $where['theme_id'] = $tplId;
            $this->view->pager = $layoutsModel->getLayoutsPager(
            $this->getRequest()->getParam('page', 1),
            $this->getRequest()->getParam('perPage', 20),
            $where
        );
    }

    /**
     * Edit Theme action
     * @return null
     */
    public function editLayoutAction()
    {
        $layoutModel = new Mode\Layouts();
        $layout = $layoutModel->getLayout($id = $this->getRequest()->getParam('id'), true);
        $form = new Templater_Form_Layout();
        $form->populate($layout->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $layoutModel->saveLayout($layout, $form->getValues());
            $this->_helper->getHelper('FlashMessenger')
                    ->addMessage('Layout successful saved.');
            $this->_redirect($this->_helper->url->url(array('action'=>'layouts','module'=>'templater'),'admin'));
        }
        $this->view->editLayoutForm = $form;
    }

    /**
     * Delete widget action
     */
    public function deleteLayoutAction()
    {
        $model = new Mode\Layouts();
        try{
            $result = $model->deleteLayout($this->getRequest()->getParam('id'), $this->getRequest());
            if ($result)
                $this->_helper->getHelper('FlashMessenger')
                        ->addMessage('Layout successful deleted.');
        } catch(Exception $exception) {
            $this->_helper->getHelper('FlashMessenger')
                    ->addMessage($exception->getMessage());
        }
        
        $this->_redirect($this->_helper->url->url(array('action'=>'layouts','module'=>'templater'),'admin'));
    }

  
    /**
     * WIDGETS SECTION
     */

    /**
     * Widgets list action
     */
    public function widgetsAction()
    {
        $widgetsModel = new Model\Widgets();
        $this->view->pager = $widgetsModel->getWidgetsPager(
                        $this->getRequest()->getParam('page', 1),
                        $this->getRequest()->getParam('perPage', 20)
        );
    }

    /**
     * Edit widget action
     * @return null
     */
    public function editWidgetAction()
    {
        $form = new Form\Widget();
        $widgetsModel = new Model\Widgets();
        $widget = $widgetsModel->getWidget($this->getRequest()->getParam('id'), true);
        $form->populate($widget->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $result = $widgetsModel->saveWidget($widget, $form->getValues());
            $this->_helper->getHelper('FlashMessenger')
                    ->addMessage('Widget successful saved.');
             $this->_redirect($this->_helper->url->url(array('action'=>'widgets','module'=>'templater'),'admin'));
        }
        $this->view->editWidgetForm = $form;
    }

    /**
     * Delete widget action
     */
    public function deleteWidgetAction()
    {
        $widgetsModel = new Model\Widgets();
        $result = $widgetsModel->deleteWidget($this->getRequest()->getParam('id'));
        if($result)
            $this->_helper->getHelper('FlashMessenger')
                    ->addMessage('Widget successful deleted.');
        $this->_redirect($this->_helper->url->url(array('action'=>'widgets','module'=>'templater'),'admin'));
    }

}