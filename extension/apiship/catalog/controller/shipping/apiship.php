<?php
namespace Opencart\Catalog\Controller\Extension\Apiship\Shipping;

/**
 * ApiShip storefront controller.
 */
class Apiship extends \Opencart\System\Engine\Controller {

	//public function __construct($params) {
	//	parent::__construct($params);

	//	$this->shipping_apiship_mode = $this->config->get('shipping_apiship_mode');

	//}

	private function check_key() {
		$this->load->language('extension/shipping/apiship');
		$results = 	['status' => 'error', 'error' => 'Invalid key'];
		if (isset($this->request->get['key'])) 
		{
			if ($this->request->get['key'] == $this->config->get('shipping_apiship_cron_key'))
			{
				$results['status'] = 'ok';				
			}
		} 
		return $results;
	}


	public function set_point(): void {
		$this->load->model('extension/apiship/shipping/apiship');
		$result = $this->model_extension_apiship_shipping_apiship->set_point();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}

	public function get_points(): void {
		$this->load->model('extension/apiship/shipping/apiship');
		$result = $this->model_extension_apiship_shipping_apiship->get_points();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}

	public function export_order() {
		$results = $this->check_key();
		if ($results['status'] == 'ok') 
		{
			$this->load->model('extension/apiship/shipping/apiship');
			$results = $this->model_extension_apiship_shipping_apiship->export_order(); 
		}

		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results)); 
	}

	public function cancel_order() {
		$results = $this->check_key();
		if ($results['status'] == 'ok') 
		{
			$this->load->model('extension/apiship/shipping/apiship');
			$results = $this->model_extension_apiship_shipping_apiship->cancel_order(); 
		}
		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results)); 
	}

	public function import_orders() {
		$results = $this->check_key();
		if ($results['status'] == 'ok') 
		{
			$this->load->model('extension/apiship/shipping/apiship');
			$results = $this->model_extension_apiship_shipping_apiship->import_orders(); 
		}

		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results)); 
	}

	public function export_orders() {
		$results = $this->check_key();
		if ($results['status'] == 'ok') 
		{
			$this->load->model('extension/apiship/shipping/apiship');
			$results = $this->model_extension_apiship_shipping_apiship->export_orders(); 
		}

		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results)); 
	}

	public function get_label() {
		$this->load->model('extension/apiship/shipping/apiship');
		$results = $this->model_extension_apiship_shipping_apiship->get_label(); 
		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results)); 

	}

	public function get_waybill() {
		$this->load->model('extension/apiship/shipping/apiship');
		$results = $this->model_extension_apiship_shipping_apiship->get_waybill(); 
		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results)); 

	}

	public function get_order_params() {
		$this->load->model('extension/apiship/shipping/apiship');
		$results = $this->model_extension_apiship_shipping_apiship->get_order_params(); 
		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results)); 

	}

	public function get_delivery_cost_original() {
		$this->load->model('extension/apiship/shipping/apiship');
		$results = $this->model_extension_apiship_shipping_apiship->get_delivery_cost_original(); 
		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results)); 

	}

	public function get_last_tracing_id() {
		$this->load->model('extension/apiship/shipping/apiship');
		$results = $this->model_extension_apiship_shipping_apiship->get_last_tracing_id(); 
		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode('x_tracing_id:' . $results)); 

	}

	public function get_point() {
		$this->load->model('extension/apiship/shipping/apiship');
		$results = $this->model_extension_apiship_shipping_apiship->get_point(); 
		$this->response->addHeader('Content-Type: application/json');		
		$this->response->setOutput(json_encode($results));
	}


}