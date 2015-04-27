<?php
class ControllerModuleMass extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('module/mass');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('mass', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
		$data['entry_admin'] = $this->language->get('entry_admin');
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['breadcrumbs'] = array();
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/mass', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$data['action'] = $this->url->link('module/mass', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['mass_admin'])) {
			$data['mass_admin'] = $this->request->post['mass_admin'];
		} else {
			$data['mass_admin'] = $this->config->get('mass_admin');
		}
		
		if (isset($this->request->post['mass_status'])) {
			$data['mass_status'] = $this->request->post['mass_status'];
		} else {
			$data['mass_status'] = $this->config->get('mass_status');
		}
		
		//get categories

		$this->load->model('catalog/category');
		$data['categories'] = $this->model_catalog_category->getCategories(array('sort'=>'name'));


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('module/mass.tpl', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/mass')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}

	public function put() {
		$this->load->model('catalog/product');

		//cria a pasta raiz 
		mkdir(DIR_IMAGE.'massupload/', 0700);
		//cria a pasta do dia
		$smallPath = 'massupload/'.time();
		$path = DIR_IMAGE . $smallPath;
		@mkdir($path, 0700);

		//move o arquivo
		rename(DIR_APPLICATION.'../server/php/files/'.$_POST['file'], $path .'/'.$_POST['file']);

		//remove a extensao pra usar como nome
		$n = explode('.', $_POST['file']);
		$nome = $n[0];

		$data['model'] = $nome;
		$data['name'] = $data['model'];
		
		$data['image'] = $smallPath.'/'.$_POST['file'];
		$data['product_category'][0] = $_POST['category_id'];
		$data['status'] = 1;
		$data['quantity'] = 100;
		$data['product_description'][1]['name'] = $data['name'];
		$data['product_description'][2]['name'] = $data['name'];
		$data['product_store'][0] = 0;
		$data['product_store'][1] = 1; 
		
		//monta as opções
		$data['product_option'][0]['value'] = 'Tamanho';
		
		$data['product_option'][0]['price'] = $_POST['price1'];
		$data['product_option'][1]['price'] = $_POST['price2'];
		$data['product_option'][2]['price'] = $_POST['price3'];
		
		$data['product_option'][0]['type'] = 'select';
		$data['product_option'][1]['type'] = 'select';
		$data['product_option'][2]['type'] = 'select';
		
		$data['product_option'][0]['option_id'] = '13';
		$data['product_option'][1]['option_id'] = '13';
		$data['product_option'][2]['option_id'] = '13';

		$data['product_option'][0]['option_value_id'] = '49';
		$data['product_option'][1]['option_value_id'] = '50';
		$data['product_option'][2]['option_value_id'] = '51';

		$data['product_option'][0]['product_option_value'][0]['price'] = $_POST['price1'];
		$data['product_option'][0]['product_option_value'][1]['price'] = $_POST['price2'];
		$data['product_option'][0]['product_option_value'][2]['price'] = $_POST['price3'];

		$data['product_option'][0]['product_option_value'][0]['quantity'] = 100;
		$data['product_option'][0]['product_option_value'][1]['quantity'] = 100;
		$data['product_option'][0]['product_option_value'][2]['quantity'] = 100;

		$data['product_option'][0]['product_option_value'][0]['option_id'] = 13;
		$data['product_option'][0]['product_option_value'][1]['option_id'] = 13;
		$data['product_option'][0]['product_option_value'][2]['option_id'] = 13;

		$data['product_option'][0]['product_option_value'][0]['option_value_id'] = '49';
		$data['product_option'][0]['product_option_value'][1]['option_value_id'] = '50';
		$data['product_option'][0]['product_option_value'][2]['option_value_id'] = '51';
		
		$data['product_option'][0]['product_option_value'][0]['price_prefix'] = '+';
		$data['product_option'][0]['product_option_value'][1]['price_prefix'] = '+';
		$data['product_option'][0]['product_option_value'][2]['price_prefix'] = '+';

		$product_id = $this->model_catalog_product->addProduct($data);

		echo 'sucesso';
	}
}