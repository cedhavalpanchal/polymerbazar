<?php
/*
@Description: Home Controller
@Author:
@Date: 7-1-2021
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('common_function_model');
    }

    /*
    @Description: Function to display main page
    @Author:
    @Date: 10-08-2016
     */
    public function index()
    {
        //echo 'Here Home page index';die;
        $this->load->view('front/include/header');
        $this->load->view('front/home/index');
        $this->load->view('front/include/footer');
    }
}
