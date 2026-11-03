<?php
namespace App\Controllers;

/** Serves the isolated browser simulation through CodeIgniter. */
class Workflow extends BaseController
{
    public function index()
    {
        $html=file_get_contents(ROOTPATH.'docs/workflow/index.html');
        return str_replace(['href="../assets/','src="../assets/'],['href="/assets/','src="/assets/'],$html);
    }
}
