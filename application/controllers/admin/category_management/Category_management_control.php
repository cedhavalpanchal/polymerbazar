<?php

/*
@Description: Category controller
@Author: Dhaval Panchal
@Input:
@Output:
@Date: 7-1-2021
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Category_management_control extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_admin_login();
        $this->viewname        = $this->router->uri->segments[2];
        $this->user_type       = 'admin';
        $this->table_name      = 'category_management';
        $this->page_title      = $this->lang->line('category_management_title');
        $this->admin_session   = $this->session->userdata($this->lang->line('business_crm_admin_session'));
        $this->message_session = $this->session->flashdata('message_session');
        // pr($this->admin_session);exit;
    }

    /*
    @Description: Function for Get Category list
    @Author: Dhaval Panchal
    @Input: - Search value or null
    @Output: - category list
    @Date: 8-1-2021
     */

    public function index()
    {

        ///Get Ajax post data
        $searchtext = $this->input->post('searchtext');
        $sortfield  = $this->input->post('sortfield');
        $sortby     = $this->input->post('sortby');
        $perpage    = $this->input->post('perpage');
        $allflag    = $this->input->post('allflag');

        if (!empty($allflag) && ($allflag == 'all' || $allflag == 'changesorting' || $allflag == 'changesearch')) {
            $this->session->unset_userdata('category_sortsearchpage_data');
        }

        $data['sortfield']  = 'id';
        $data['sortby']     = 'desc';
        $searchsort_session = $this->session->userdata('category_sortsearchpage_data');

        if (!empty($sortfield) && !empty($sortby)) {
            $data['sortfield'] = $sortfield;
            $data['sortby']    = $sortby;
        } else {

            if (!empty($searchsort_session['sortfield'])) {
                if (!empty($searchsort_session['sortby'])) {
                    $data['sortfield'] = $searchsort_session['sortfield'];
                    $data['sortby']    = $searchsort_session['sortby'];
                    $sortfield         = $searchsort_session['sortfield'];
                    $sortby            = $searchsort_session['sortby'];
                }
            } else {
                $sortfield = 'id';
                $sortby    = 'desc';
            }
        }

        if (!empty($searchtext)) {
            $data['searchtext'] = $searchtext;
        } else {

            if (empty($allflag)) {
                if (!empty($searchsort_session['searchtext'])) {
                    $data['searchtext'] = $searchsort_session['searchtext'];
                    $searchtext         = $data['searchtext'];
                } else {
                    $data['searchtext'] = '';
                }
            } else {
                $data['searchtext'] = '';
            }
        }

        if (!empty($perpage) && $perpage != 'null') {
            $data['perpage']    = $perpage;
            $config['per_page'] = $perpage;
        } else {

            if (!empty($searchsort_session['perpage'])) {
                $data['perpage']    = trim($searchsort_session['perpage']);
                $config['per_page'] = trim($searchsort_session['perpage']);
            } else {
                $config['per_page'] = PAGINATION_SIZE;
                $data['perpage']    = PAGINATION_SIZE;
            }
        }

        $config['base_url']        = site_url($this->user_type . '/' . $this->viewname);
        $config['is_ajax_paging']  = true; // default FALSE
        $config['paging_function'] = 'ajax_paging'; // Your jQuery paging

        if (!empty($allflag) && ($allflag == 'all' || $allflag == 'changesorting' || $allflag == 'changesearch')) {
            $config['uri_segment'] = 0;
            $uri_segment           = 0;
        } else {
            $config['uri_segment'] = 3;
            $uri_segment           = $this->uri->segment(3);
        }

        $fields = array('*');

        $where = '';

        if (!empty($searchtext)) {
            $searchkeyword = mysqli_real_escape_string($this->db->conn_id, (trim(stripslashes($searchtext))));
            $where         = '(name LIKE "%' . $searchkeyword . '%") ';
        }

        //Get All Category
        $sq_data_all = array
            (
            "table"       => 'category_management',
            "fields"      => $fields,
            "num"         => $config['per_page'],
            "offset"      => $uri_segment,
            "orderby"     => $sortfield,
            "sort"        => $sortby,
            "wherestring" => $where,
        );
        $data['datalist']        = $this->Common_function_model->getmultiple_tables($sq_data_all);
        $sq_data_all['offset']   = '';
        $sq_data_all['num']      = '';
        $sq_data_all['totalrow'] = '1';
        $config['total_rows']    = $this->Common_function_model->getmultiple_tables($sq_data_all);

        ///Prepare Paginations
        $this->pagination->initialize($config);
        $data['pagination']  = $this->pagination->create_links();
        $data['uri_segment'] = $uri_segment;

        //Set Session
        $category_sortsearchpage_data = array(
            'sortfield'   => $data['sortfield'],
            'sortby'      => $data['sortby'],
            'searchtext'  => $data['searchtext'],
            'perpage'     => trim($data['perpage']),
            'uri_segment' => $uri_segment,
            'total_rows'  => $config['total_rows']);
        $this->session->set_userdata('category_sortsearchpage_data', $category_sortsearchpage_data);

        if ($this->input->post('result_type') == 'ajax') {
            $this->load->view($this->user_type . '/' . $this->viewname . '/ajax_list', $data);
        } else {
            $data['main_content'] = $this->user_type . '/' . $this->viewname . "/list";
            $this->load->view('admin/include/template', $data);
        }
    }

    /*
    @Description: Function Add Category
    @Author: Dhaval Panchal
    @Date: 8-1-2021
     */

    public function add_record()
    {

        $data['main_content'] = "admin/" . $this->viewname . "/add";
        $data['foot_part_js'] = 'category_add';
        $this->load->view('admin/include/template', $data);
    }

    /*
    @Description: Function for Insert New admin
    @Author: Dhaval Panchal
    @Input: - Details of new User which is inserted into DB
    @Output: - List of User with new inserted records
    @Date: 8-1-2021
     */

    public function insert_data()
    {
        // pr($_FILES);
        // pr($_POST);exit;

        $this->load->library('form_validation');

        if ($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->post('save') === 'submitForm') {
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');

            if ($this->form_validation->run() == false) {
                $data['main_content'] = $this->user_type . '/' . $this->viewname . "/add";
                $this->load->view($this->user_type . '/include/template', $data);
            } else {
                ///Insert New Category
                $data = array
                    (
                    "name" => $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('name')))))),
                    // "parent_id" => $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('parent_id')))))),
                );

                $inserted_data = $this->Common_function_model->insert($this->table_name, $data);

                if (!empty($inserted_data)) {
                    ////////////
                    $response = array(
                        "status"  => $this->lang->line('message_type_success'),
                        "message" => $this->lang->line('common_add_success_msg'),
                    );
                    $this->session->set_flashdata('message_session', $response);
                    redirect($this->user_type . '/' . $this->viewname);
                } else {
                    $response = array(
                        "status"  => $this->lang->line('message_type_failed'),
                        "message" => $this->lang->line('common_error_msg'),
                    );
                    $this->session->set_flashdata('message_session', $response);
                    redirect($this->user_type . '/' . $this->viewname);
                }
            }
        } else {
            $response = array(
                "status"  => $this->lang->line('message_type_failed'),
                "message" => $this->lang->line('common_error_msg'),
            );
            $this->session->set_flashdata('message_session', $response);
            redirect($this->user_type . '/' . $this->viewname);
        }
    }

    /*
    @Description: Edit category form
    @Author: Dhaval Panchal
    @Date: 8-1-2021
     */

    public function edit_record()
    {
        $id          = $this->uri->segment(4);
        $match       = array('id' => $id);
        $sq_data_all = array
            (
            "table"     => $this->table_name,
            "condition" => $match,
        );
        $result = $this->Common_function_model->getmultiple_tables($sq_data_all);

        if (empty($result)) {
            redirect($this->user_type . '/' . $this->viewname);
        }

        $data['editRecord']   = $result;
        $data['foot_part_js'] = 'category_add';
        $data['main_content'] = $this->user_type . '/' . $this->viewname . "/add";
        $this->load->view($this->user_type . '/include/template', $data);
    }

    /*
    @Description: Function for update category
    @Author: Dhaval Panchal
    @Date: 8-1-2021
     */

    public function update_data()
    {

        $this->load->library('form_validation');

        //Check user
        $cdata['id'] = $this->input->post('id');
        $match       = array('id' => $cdata['id']);
        $sq_data_all = array
            (
            "table"     => $this->table_name,
            "condition" => $match,
        );
        $result = $this->Common_function_model->getmultiple_tables($sq_data_all);

        if (empty($result)) {
            $response = array(
                "status"  => $this->lang->line('message_type_failed'),
                "message" => $this->lang->line('common_error_msg'),
            );
            $this->session->set_flashdata('message_session', $response);
            redirect($this->user_type . '/' . $this->viewname);
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->post('save') === 'submitForm') {
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');

            if ($this->form_validation->run() == false) {
                $data['editRecord']   = $result;
                $data['main_content'] = $this->user_type . '/' . $this->viewname . "/add";
                $this->load->view($this->user_type . '/include/template', $data);
            } else {
                $cdata['name'] = $this->security->xss_clean(strip_tags(addslashes(trim(strtolower($this->input->post('name'))))));
                $this->Common_function_model->update($this->table_name, $cdata, array('id' => $cdata['id']));

                $response = array(
                    "status"  => $this->lang->line('message_type_success'),
                    "message" => $this->lang->line('common_edit_success_msg'),
                );
                $this->session->set_flashdata('message_session', $response);
                $searchsort_session = $this->session->userdata('category_sortsearchpage_data');
                $pagingid           = $searchsort_session['uri_segment'];
                redirect($this->user_type . '/' . $this->viewname . '/' . $pagingid);
            }
        } else {
            $response = array(
                "status"  => $this->lang->line('message_type_failed'),
                "message" => $this->lang->line('common_error_msg'),
            );
            $this->session->set_flashdata('message_session', $response);
            redirect($this->user_type . '/' . $this->viewname);
        }
    }

    /*
    @Description: Function for Active and Inactive By Admin
    @Author: Dhaval Panchal
    @Date: 8-1-2021
     */

    public function status_update()
    {
        $id = $this->uri->segment(4);

        $cdata['id']     = $id;
        $cdata['status'] = $this->input->post('status');
        $this->Common_function_model->update($this->table_name, $cdata, array('id' => $cdata['id']));

        $searchsort_session = $this->session->userdata('category_sortsearchpage_data');

        if (!empty($searchsort_session['uri_segment'])) {
            $pagingid = $searchsort_session['uri_segment'];
        } else {
            $pagingid = 0;
        }

        echo $pagingid;
    }

    /*
    @Description: Function for Bulk action to delete admin
    @Author: Dhaval Panchal
    @Date: 8-1-2021
     */
    public function ajax_delete_all()
    {
        $admin = $this->session->userdata($this->lang->line('business_crm_admin_session'));
        $id    = $this->input->post('single_remove_id');

        if (!empty($id) && $admin['id'] != $id) {
            $this->Common_function_model->delete($this->table_name, array('id' => $id));
            unset($id);
        }

        $array_data = $this->input->post('myarray');

        if (!empty($array_data)) {
            for ($i = 0; $i < count($array_data); $i++) {
                if (!empty($array_data[$i]) && $array_data[$i] != $admin['id']) {
                    $this->Common_function_model->delete($this->table_name, array('id' => $array_data[$i]));
                }
            }
        }

        $searchsort_session = $this->session->userdata('category_sortsearchpage_data');

        if (!empty($searchsort_session['uri_segment'])) {
            $pagingid = $searchsort_session['uri_segment'];
        } else {
            $pagingid = 0;
        }

        echo $pagingid;
    }

    /*
    @Description: Function for Multiple Active and Inactive By Admin
    @Author: Dhaval Panchal
    @Date: 8-1-2021
     */
    public function ajax_status_all()
    {
        $array_data      = $this->input->post('myarray');
        $cdata['status'] = $this->input->post('status');

        for ($i = 0; $i < count($array_data); $i++) {
            if (!empty($array_data[$i])) {
                $this->Common_function_model->update($this->table_name, $cdata, array('id' => $array_data[$i]));
            }
        }

        $searchsort_session = $this->session->userdata('category_sortsearchpage_data');
        echo $pagingid      = !empty($searchsort_session['uri_segment']) ? $searchsort_session['uri_segment'] : 0;
    }
}
