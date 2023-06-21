<?php
/**
 * Description of LanguageLoader
 *
 * @author asimriaz
 */
class Language_Loader
{
    function initialize()
    {
        $CI =& get_instance();
        $CI->load->helper('language');
        //$ci->lang->load('en_site','english');
        //echo "sesssion lang:  " .  $CI->session->userdata('site_lang');exit;

        // Set API response lang
        $api_response_lang = NULL;
        $headers = $CI->input->request_headers();
        if (array_key_exists('Response-Lang', $headers))
        {
            $api_response_lang = $headers['Response-Lang'];
        }

        $site_lang = $CI->session->userdata('site_lang');
        if ($site_lang == 'english' OR $api_response_lang === 'en')
        {
            $CI->lang->load('en_site', 'english');
            $CI->config->set_item('language', 'english');
        }
        else if ($site_lang == 'swedish' OR $api_response_lang === 'sv')
        {
            $CI->lang->load('sv_site', 'swedish');
            $CI->config->set_item('language', 'swedish');
        }
        else
        {
            $CI->lang->load('sv_site','swedish');
            $CI->config->set_item('language', 'swedish');
        }
    }
}
