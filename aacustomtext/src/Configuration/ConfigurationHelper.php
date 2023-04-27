<?php

namespace Aality\CustomText\Configuration;

use AdminController;
use Configuration;
use HelperForm;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Tools;
use Uploader;
use Validate;

class ConfigurationHelper
{

    /**
     * @var \aaCustomText
     */
    private $module;

    private $output = '';

    private $fields = [];

    public function __construct($module)
    {
        $this->module = $module;
        $this->initFields();
    }

    /**
     * Do not modify
     *
     * @return string
     */
    public function getForm()
    {
        # Handle form submission
        $this->handleFormSubmission();

        # Init HelperForm
        $helper = new HelperForm();

        # Module, token and currentIndex
        $helper->name_controller = $this->module->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->module->name]);
        $helper->submit_action = 'submit' . ucfirst($this->module->name) . 'Form';

        # Default language
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');

        # Load current value into the form
        foreach ($this->fields as $field) {
            $helper->fields_value[$field['name']] = Tools::getValue($field['name'], Configuration::get($field['name']));
        }

        # Create form
        $form = [
            'form' => [
                'tinymce' => true,
                'legend' => [
                    'title' => $this->module->l('Settings'),
                ],
                'input' => $this->fields,
                'submit' => [
                    'title' => $this->module->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        # Return output
        return $this->getConfigurationPageHeader() . $this->output . $helper->generateForm([$form]);
    }

    /**
     * Do not modify
     */
    private function handleFormSubmission()
    {
        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . ucfirst($this->module->name) . 'Form')) {

            $success = true;

            foreach ($this->fields as $field) {

                # Retrieve the value set by the user
                $value = Tools::getValue($field['name']);

                # Check that the value is valid
                if ($field['required'] && empty($value)) {
                    $success = false;
                } else {
                    if (empty($value) && $field['type'] == 'password') {
                        continue;
                    }

                    if ($field['type'] == 'file' && isset($_FILES[$field['name']])) {
                        $this->uploadFile($field['name']);
                    }

                    # Value is ok, update it
                    Configuration::updateValue($field['name'], $value);
                }
            }

            # Display confirmation or error message
            $this->output = $success ? $this->module->displayConfirmation($this->module->l('Settings updated')) : $this->module->displayError($this->module->l('Invalid Configuration value'));

        }
    }

    /**
     * Initiate fields here, everything else is handle automatically
     */
    private function initFields()
    {
        $this->fields = [

            [
                'name' => 'CUSTOM_TEXT_TITRE_1',
                'type' => 'text',
                'label' => $this->module->l('Titre de la section'),
                'required' => true,
            ],
            [
                'name' => 'CUSTOM_TEXT_DESCRIPTION_1',
                'type' => 'textarea',
                'label' => $this->module->l('Description de la section'),
                'required' => true,
                'cols' => 40,
                'rows' => 10,
                'class' => 'rte',
                'autoload_rte' => true,
            ],


            [
                'name' => 'CUSTOM_TEXT_TITRE_2',
                'type' => 'text',
                'label' => $this->module->l('Titre de la section'),
                'required' => true,
            ],
            [
                'name' => 'CUSTOM_TEXT_DESCRIPTION_2',
                'type' => 'textarea',
                'label' => $this->module->l('Description de la section'),
                'required' => true,
                'cols' => 40,
                'rows' => 10,
                'class' => 'rte',
                'autoload_rte' => true,
            ],

            [
                'name' => 'CUSTOM_TEXT_TITRE_3',
                'type' => 'text',
                'label' => $this->module->l('Titre de la section'),
                'required' => true,
            ],
            [
                'name' => 'CUSTOM_TEXT_DESCRIPTION_3',
                'type' => 'textarea',
                'label' => $this->module->l('Description de la section'),
                'required' => true,
                'cols' => 40,
                'rows' => 10,
                'class' => 'rte',
                'autoload_rte' => true,
            ],

            [
                'name' => 'CUSTOM_TEXT_TITRE_4',
                'type' => 'text',
                'label' => $this->module->l('Titre de la section'),
                'required' => true,
            ],
            [
                'name' => 'CUSTOM_TEXT_DESCRIPTION_4',
                'type' => 'textarea',
                'label' => $this->module->l('Description de la section'),
                'required' => true,
                'cols' => 40,
                'rows' => 10,
                'class' => 'rte',
                'autoload_rte' => true,
            ],

        ];
    }

    /**
     * @param string $fileName
     */
    private function uploadFile(string $fileName)
    {
        $uploader = new Uploader($fileName);
        $uploader
            //->setAcceptTypes('jpg') // Extensions supportées
            ->setMaxSize(Uploader::DEFAULT_MAX_SIZE)
            ->setSavePath(_PS_MODULE_DIR_ . $this->module->name . '/uploads/')
            ->process();
    }

    /**
     * @return bool|mixed|string|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function getConfigurationPageHeader()
    {
        # Check if already loaded in cache
        $cacheId = $this->module->name . '_moduleheadercontent';
        $cache = new FilesystemCache($cacheId, 0, _PS_CACHE_DIR_ . $this->module->name . 'module');
        $force = \Tools::getIsset('refresh');

        # If we have to fetch the data
        if (!$cache->has($cacheId) || $force) {

            # Get the content
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'www.aality.fr/embed/prestashop/module-header.html',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);
            $html = curl_exec($curl);
            curl_close($curl);

            # If we have some content, save in cache for 48 hours
            if ($html) {
                $cache->set($cacheId, $html, 172800);
            }
            return $html;
        }
        # Return from cache
        return $cache->get($cacheId);
    }
}
