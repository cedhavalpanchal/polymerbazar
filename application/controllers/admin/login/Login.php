<?php

/*
@Description: login controller
@Author: Dhaval Panchal
@Date: 24-11-2016
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->message_session = $this->session->flashdata('message_session');
        $this->table_name      = 'admin_management';
    }

    public function index()
    {

        $admin_session = $this->session->userdata($this->lang->line('business_crm_admin_session'));

        if ($admin_session['active'] === true) {
            redirect('admin/dashboard');
        } else {
            $this->do_login();
        }
    }

    /*
    @Description: login function
    @Author: Dhaval Panchal
    @Input:username, password
    @Output:
    @Date: 24-11-2016
     */
    public function do_login()
    {
        $email        = $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('email'))))));
        $password     = $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('password'))))));
        $password     = $this->Common_function_model->encrypt_script($password);
        $forgot_email = $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('forgot_email'))))));

        if ($forgot_email) {
            $this->forgetpw_action($forgot_email);
        } else {

            if ($email && $password) {
                $field = array('*', 'CONCAT_WS(" ",first_name,last_name) as name');
                $match = array('email' => $email, 'password' => $password);

                $sq_data_all = array
                    (
                    "table"     => 'admin_management',
                    "fields"    => $field,
                    "condition" => $match,
                );

                $udata = $this->Common_function_model->getmultiple_tables($sq_data_all);

                if (count($udata) > 0) {
                    if ($udata[0]['status'] == 1) {
                        $newdata              = $udata[0];
                        $newdata['useremail'] = $udata[0]['email'];
                        $newdata['active']    = true;

                        $this->session->set_userdata($this->lang->line('business_crm_admin_session'), $newdata);
                        $referred_from = $this->session->userdata('current_url_session');

                        $this->session->unset_userdata('current_url_session');
                        redirect('admin/dashboard');

                        // if (!empty($referred_from)) {
                        //     $this->session->unset_userdata('current_url_session');
                        //     redirect($referred_from);
                        // }else{
                        //     redirect('admin/dashboard');
                        // }
                    } else {
                        $response = array(
                            "status"  => $this->lang->line('message_type_failed'),
                            "message" => $this->lang->line('inactive_account'),
                        );
                        $this->session->set_flashdata('message_session', $response);
                        $this->load->view('admin/login/login');
                    }
                } else {
                    $response = array(
                        "status"  => $this->lang->line('message_type_failed'),
                        "message" => $this->lang->line('invalid_us_pass'),
                    );
                    $this->session->set_flashdata('message_session', $response);
                    $this->load->view('admin/login/login');
                }
            } else {
                $this->load->view('admin/login/login');
            }
        }
    }

    /*
    @Description : Function to generate password and email the same to user
    @Author      : Dhaval Panchal
    @Input       : email address
    @Output      : password to the email address
    @Date        : 06-05-14
     */
    public function forgetpw_action($email)
    {
        $match       = array('email' => $email);
        $sq_data_all = array
            (
            "table"     => 'admin_management',
            "condition" => $match,
        );
        $result = $this->Common_function_model->getmultiple_tables($sq_data_all);

        if ((count($result)) > 0) {
            $name = $result[0]['first_name'];

            if ($result[0]['status'] == 1) {
                $encBlastId = $this->Common_function_model->encrypt_script($result[0]['id']);
                // Email Start

                $loginLink = $this->config->item('base_url') . 'admin/reset_password/' . $encBlastId;

                $pass_variable_activation = array('name' => $name, 'email' => $email, 'loginLink' => $loginLink);
                $data['actdata']          = $pass_variable_activation;
                $activation_tmpl          = $this->load->view('email_template/reset_password', $data, true);
                $sub                      = $this->config->item('sitename') . " : Admin Forgot Password";

                $from     = $this->config->item('admin_email');
                $sendmail = $this->Common_function_model->send_email($email, $sub, $activation_tmpl, $from);
                $msg      = "Mail Sent Successfully";
                $status   = $this->lang->line('message_type_success');
            } else {
                $msg    = $this->lang->line('inactive_account');
                $status = $this->lang->line('message_type_failed');
            }
        } else {
            $msg    = "No Such User Found";
            $status = $this->lang->line('message_type_failed');
        }

        $response = array(
            "status"  => $status,
            "message" => $msg,
        );
        $this->session->set_flashdata('message_session', $response);
        redirect('admin/login');
    }

    public function reset_password($token)
    {
        $admin_id = $this->Common_function_model->decrypt_script($token);

        if (!empty($admin_id)) {
            $view_data['token'] = $admin_id;
            $this->load->view('admin/login/reset_password', $view_data);
        } else {
            $response = array(
                "message" => "No Such User Found",
                "status"  => $this->lang->line('message_type_failed'),
            );
            $this->session->set_flashdata('message_session', $response);
            redirect('admin/login');
        }
    }

    public function change_password()
    {

        $admin_id    = $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('token'))))));
        $password    = $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('password'))))));
        $cnfpassword = $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('cnf_password'))))));
        // pr($_POST);exit;

        if (!empty($password) && !empty($cnfpassword) && !empty($admin_id)) {
            $password = $this->Common_function_model->encrypt_script($password);
            // $admin_id = $this->Common_function_model->decrypt_script($token);
            // echo $token;exit;
            $update['password'] = $password;
            $this->Common_function_model->update('admin_management', $update, '', " id = $admin_id ");
            // echo $this->db->last_query();exit;
            $response = array(
                "message" => "Password changed Successfully",
                "status"  => $this->lang->line('message_type_failed'),
            );
        } else {
            $response = array(
                "message" => "Password and confirm password is required",
                "status"  => $this->lang->line('message_type_failed'),
            );
        }

        $this->session->set_flashdata('message_session', $response);
        redirect('admin/login');
    }
}
