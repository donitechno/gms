<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class m_pdf {
    
    function m_pdf_class()
    {
        $CI = & get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }
 
    function load()
    {
        include_once APPPATH.'/third_party/mpdf/mpdf.php';
         
        if ($params == NULL)
        {
            $param = '"en-GB-x","A4","0","0",0,0,0,0,0,0';          
        }
         
		// return new mPDF('','', 0, '', 5, 5, 5, 5, 5, 5, 'L');
		return new mPDF('utf-8', "A4", 0, '', 10, 10, 10, 15, 5, 5, 'L');
    }
}