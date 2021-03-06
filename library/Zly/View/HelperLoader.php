<?php

namespace Zly\View;

class HelperLoader extends \Zend\Loader\PluginClassLoader
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $plugins = array(
        'action'              => 'Zend\View\Helper\Action',
        'baseurl'             => 'Zend\View\Helper\BaseUrl',
        'currency'            => 'Zend\View\Helper\Currency',
        'cycle'               => 'Zend\View\Helper\Cycle',
        'declarevars'         => 'Zend\View\Helper\DeclareVars',
        'doctype'             => 'Zend\View\Helper\Doctype',
        'fieldset'            => 'Zend\View\Helper\Fieldset',
        'formbutton'          => 'Zend\View\Helper\FormButton',
        'formcheckbox'        => 'Zend\View\Helper\FormCheckbox',
        'formerrors'          => 'Zend\View\Helper\FormErrors',
        'formfile'            => 'Zend\View\Helper\FormFile',
        'formhidden'          => 'Zend\View\Helper\FormHidden',
        'formimage'           => 'Zend\View\Helper\FormImage',
        'formlabel'           => 'Zend\View\Helper\FormLabel',
        'formmulticheckbox'   => 'Zend\View\Helper\FormMultiCheckbox',
        'formnote'            => 'Zend\View\Helper\FormNote',
        'formpassword'        => 'Zend\View\Helper\FormPassword',
        'formradio'           => 'Zend\View\Helper\FormRadio',
        'formreset'           => 'Zend\View\Helper\FormReset',
        'formselect'          => 'Zend\View\Helper\FormSelect',
        'formsubmit'          => 'Zend\View\Helper\FormSubmit',
        'formtextarea'        => 'Zend\View\Helper\FormTextarea',
        'formtext'            => 'Zend\View\Helper\FormText',
        'form'                => 'Zend\View\Helper\Form',
        'headlink'            => 'Zend\View\Helper\HeadLink',
        'headmeta'            => 'Zend\View\Helper\HeadMeta',
        'headscript'          => 'Zend\View\Helper\HeadScript',
        'headstyle'           => 'Zend\View\Helper\HeadStyle',
        'headtitle'           => 'Zend\View\Helper\HeadTitle',
        'htmlflash'           => 'Zend\View\Helper\HtmlFlash',
        'htmllist'            => 'Zend\View\Helper\HtmlList',
        'htmlobject'          => 'Zend\View\Helper\HtmlObject',
        'htmlpage'            => 'Zend\View\Helper\HtmlPage',
        'htmlquicktime'       => 'Zend\View\Helper\HtmlQuicktime',
        'inlinescript'        => 'Zend\View\Helper\InlineScript',
        'json'                => 'Zend\View\Helper\Json',
        'layout'              => 'Zend\View\Helper\Layout',
        'navigation'          => 'Zend\View\Helper\Navigation',
        'paginationcontrol'   => 'Zend\View\Helper\PaginationControl',
        'partialloop'         => 'Zend\View\Helper\PartialLoop',
        'partial'             => 'Zend\View\Helper\Partial',
        'placeholder'         => 'Zend\View\Helper\Placeholder',
        'rendertoplaceholder' => 'Zend\View\Helper\RenderToPlaceholder',
        'serverurl'           => 'Zend\View\Helper\ServerUrl',
        'translate'           => 'Zend\View\Helper\Translate',
        'url'                 => 'Zend\View\Helper\Url',
        'tree'                => 'Zly\View\Helper\Tree',
    );
}
