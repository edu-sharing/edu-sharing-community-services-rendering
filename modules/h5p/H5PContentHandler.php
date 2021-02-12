<?php

namespace connector\tools\h5p;

class H5PContentHandler {

private $content = NULL;

    public function __construct() {
        global $h5pLang;
        $this->H5PFramework = new H5PFramework();
        $this->H5PCore = new \H5PCore($this->H5PFramework, $this->H5PFramework->get_h5p_path(), $this->H5PFramework->get_h5p_url(), $h5pLang, true);
        $this->H5PCore->aggregateAssets = true; // why not?
        $this->H5PValidator = new \H5PValidator($this->H5PFramework, $this->H5PCore);
        $this->H5peditorStorageImpl = new H5peditorStorageImpl();
        $this->H5PEditorAjaxImpl = new H5PEditorAjaxImpl();
        $this->H5PEditor = new \H5peditor( $this->H5PCore, $this->H5peditorStorageImpl, $this->H5PEditorAjaxImpl);
        $this->H5PExport = new \H5PExport($this->H5PFramework, $this->H5PCore);

    }



    public function process_new_content() {

        // Check if we have any content or errors loading content
      /*  $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($id) {
            $this->load_content($id);
            if (is_string($this->content)) {
                H5P_Plugin_Admin::set_error($this->content);
                $this->content = NULL;
            }
        }

        if ($this->content !== NULL) {
            // We have existing content

            if (!$this->current_user_can_edit($this->content)) {
                // The user isn't allowed to edit this content
                H5P_Plugin_Admin::set_error(__('You are not allowed to edit this content.', $this->plugin_slug));
                return;
            }

            // Check if we're deleting content
            $delete = filter_input(INPUT_GET, 'delete');
            if ($delete) {
                if (wp_verify_nonce($delete, 'deleting_h5p_content')) {
                    $this->H5peditorStorageImpl->deletePackage($this->content);
                    wp_safe_redirect(admin_url('admin.php?page=h5p'));
                    return;
                }
                H5P_Plugin_Admin::set_error(__('Invalid confirmation code, not deleting.', $this->plugin_slug));
            }
        }*/

        // Check if we're uploading or creating content
        $action = 'create';//filter_input(INPUT_POST, 'action', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^(upload|create)$/')));
        if ($action) {
           // check_admin_referer('h5p_content', 'yes_sir_will_do'); // Verify form
            $result = FALSE;
            if ($action === 'create') {
                // Handle creation of new content.
                $result = $this->handle_content_creation($this->content);
            }
           /* elseif (isset($_FILES['h5p_file']) && $_FILES['h5p_file']['error'] === 0) {
                // Create new content if none exists
                $content = ($this->content === NULL ? array('disable' => H5PCore::DISABLE_NONE) : $this->content);
                $content['title'] = $this->get_input_title();
                $content['uploaded'] = true;
                $this->get_disabled_content_features($core, $content);

                // Handle file upload
                $plugin_admin = H5P_Plugin_Admin::get_instance();
                $result = $plugin_admin->handle_upload($content);
            }*/
            if ($result) {
                $content['id'] = $result;
           //     $this->set_content_tags($content['id'], filter_input(INPUT_POST, 'tags'));
            //    wp_safe_redirect(admin_url('admin.php?page=h5p&task=show&id=' . $result));
            return $content['id'];            }
        }
    }
    #
    /**
     * Create new content.
     *
     * @since 1.1.0
     * @param array $content
     * @return mixed
     */
    private function handle_content_creation($content) {


        // Keep track of the old library and params
        $oldLibrary = NULL;
        $oldParams = NULL;
        if ($content !== NULL) {
            $oldLibrary = $content['library'];
            $oldParams = json_decode($content['params']);
        }
        else {
            $content = array(
                'disable' => \H5PCore::DISABLE_NONE
            );
        }

        // Get library
        error_log("handle content creation: ".$_REQUEST['library']);
        $content['library'] = $this->H5PCore->libraryFromString($_REQUEST['library']);
        if (!$content['library']) {
           // $this->H5PCore->h5pF->setErrorMessage(__('Invalid library.', $this->plugin_slug));
            return FALSE;
        }

        // Check if library exists.
        $content['library']['libraryId'] = $this->H5PCore->h5pF->getLibraryId($content['library']['machineName'], $content['library']['majorVersion'], $content['library']['minorVersion']);
        if (!$content['library']['libraryId']) {
            $this->H5PCore->h5pF->setErrorMessage(__('No such library.', $this->plugin_slug));
            return FALSE;
        }

        // Get title
        $content['title'] = $content['slug'] = $_REQUEST['title'];
        if ($content['title'] === NULL) {
            return FALSE;
        }

        // Check parameters
        $content['params'] = $_REQUEST['parameters'];
        if ($content['params'] === NULL) {
            return FALSE;
        }
        $params = json_decode($content['params']);
        if ($params === NULL) {
            //$this->H5PCore->h5pF->setErrorMessage(__('Invalid parameters.', $this->plugin_slug));
            return FALSE;
        }

        $content['params'] = json_encode($params->params);
        $content['metadata'] = (array)$params->metadata;

        // Set disabled features
       // $this->get_disabled_content_features($core, $content);

        // Save new content
        $content['id'] = $this->H5PCore->saveContent($content);

        $content['library']['name'] = $content['library']['machineName'];
        $content['embedType'] = 'iframe';

        // Move images and find all content dependencies
        $this->H5PEditor->processParameters($content['id'], $content['library'], $params->params, $oldLibrary, $oldParams);
        $this->H5PCore->filterParameters($content);

        return $content['id'];
    }


}