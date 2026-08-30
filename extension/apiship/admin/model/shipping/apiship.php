<?php
namespace Opencart\Admin\Model\Extension\Apiship\Shipping;

require_once DIR_EXTENSION . 'apiship/system/library/apiship.php';

/**
 * ApiShip admin model.
 */
class Apiship extends \Opencart\System\Engine\Model {

	private $apiship;
	private $apiship_params = [];
	private $filterit = false;

	public function __construct($params) {
		parent::__construct($params);
		$this->apiship_params = [
			'shipping_apiship_rub_select' => $this->config->get('shipping_apiship_rub_select'),
			'shipping_apiship_gr_select' => $this->config->get('shipping_apiship_gr_select'),
			'shipping_apiship_cm_select' => $this->config->get('shipping_apiship_cm_select'),

			'shipping_apiship_token' => $this->config->get('shipping_apiship_token'),
			'shipping_apiship_mode' => $this->config->get('shipping_apiship_mode'),
			'shipping_apiship_provider' => $this->config->get('shipping_apiship_provider'),
			'shipping_apiship_prefix' => $this->config->get('shipping_apiship_prefix')
		];
		$this->apiship = new \Apiship($this->registry, $this->apiship_params, $this->log);
	}

	public function install() {

		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."apiship_order` ( ";
		$sql .= " `oc_order_id` int(11) NOT NULL,";
		$sql .= " `apiship_order_id` int(11) NOT NULL,";
 		$sql .= " `status` int(11) DEFAULT NULL,";
		$sql .= " UNIQUE KEY `oc_order_id` (`oc_order_id`) ";
		$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
		$this->db->query($sql);

		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."apiship_order_status` (";
		$sql .= " `id` int(11) NOT NULL AUTO_INCREMENT,";
		$sql .= " `key` varchar(64) COLLATE utf8_unicode_ci NOT NULL,";
		$sql .= " `name` text COLLATE utf8_unicode_ci NOT NULL,";
		$sql .= " PRIMARY KEY (`id`),";
		$sql .= " UNIQUE KEY `state_key` (`key`)";
		$sql .= " ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$this->db->query($sql);

		// Расширение итогов «Обрешётка» (отдельная строка в расчёте заказа).
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "extension` (`extension`, `type`, `code`) VALUES ('apiship', 'total', 'crate')");

		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'total', 'total_crate_status', '1', 0)");
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'total', 'total_crate_sort_order', '2', 0)");
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'total', 'total_crate_title', 'Обрешётка', 0)");
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'shipping_apiship', 'shipping_apiship_crate_dimension_add', '10', 0)");
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'shipping_apiship', 'shipping_apiship_crate_weight_add', '0', 0)");

		// Регистрация модуля доставки «ApiShip» (Extension → Shipping).
		// БЕЗ этого после переустановки модуль исчезает из списка доставки,
		// хотя файлы на месте и установщик показывает его «установленным».
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "extension` (`extension`, `type`, `code`) VALUES ('apiship', 'shipping', 'apiship')");

		// Восстановление настроек модуля, если их удалила переустановка.
		// ВАЖНО: значения токена, адреса отправки и контактов здесь НЕ задаются —
		// они вводятся в админ-панели (Расширения → Доставка → ApiShip).
		$apiship_settings = [
			['shipping_apiship_token', '', 0],
			['shipping_apiship_title', 'Доставка', 0],
			['shipping_apiship_status', '1', 0],
			['shipping_apiship_rub_select', 'RUB', 0],
			['shipping_apiship_gr_select', '2', 0],
			['shipping_apiship_cm_select', '1', 0],
			['shipping_apiship_include_fees', '1', 0],
			['shipping_apiship_group_points', '1', 0],
			['shipping_apiship_title_point_template', 'Доставка до ПВЗ %name', 0],
			['shipping_apiship_description_point_template', '', 0],
			['shipping_apiship_title_door_template', 'Доставка курьером', 0],
			['shipping_apiship_description_door_template', '', 0],
			['shipping_apiship_icon_show', '1', 0],
			['shipping_apiship_sending_country_code', 'RU', 0],
			['shipping_apiship_sending_region', '', 0],
			['shipping_apiship_sending_city', '', 0],
			['shipping_apiship_sending_street', '', 0],
			['shipping_apiship_sending_house', '', 0],
			['shipping_apiship_sending_block', '', 0],
			['shipping_apiship_sending_office', '', 0],
			['shipping_apiship_contact_organization', '', 0],
			['shipping_apiship_contact_inn', '', 0],
			['shipping_apiship_contact_name', '', 0],
			['shipping_apiship_contact_phone', '', 0],
			['shipping_apiship_contact_email', '', 0],
			['shipping_apiship_parcel_length', '20', 0],
			['shipping_apiship_parcel_width', '20', 0],
			['shipping_apiship_parcel_height', '50', 0],
			['shipping_apiship_parcel_weight', '1000', 0],
			['shipping_apiship_place_length', '', 0],
			['shipping_apiship_place_width', '', 0],
			['shipping_apiship_place_height', '', 0],
			['shipping_apiship_place_weight', '', 0],
			['shipping_apiship_package_weight', '', 0],
			['shipping_apiship_articul_mode', '0', 0],
			['shipping_apiship_tax_class_id', '0', 0],
			['shipping_apiship_geo_zone_id', '0', 0],
			['shipping_apiship_prefix', '', 0],
			['shipping_apiship_error_stub_show', '1', 0],
			['shipping_apiship_use_fix_product_assessed_cost', '1', 0],
			['shipping_apiship_fix_product_assessed_cost', '1000', 0],
			['shipping_apiship_add_pickup_date', '1', 0],
			['shipping_apiship_export_status', '3', 0],
			['shipping_apiship_cancel_export_status', '7', 0],
			['shipping_apiship_group_export_status_ready', '1', 0],
			['shipping_apiship_group_export_status_ok', '3', 0],
			['shipping_apiship_group_export_status_error', '7', 0],
			['shipping_apiship_cron_key', '', 0],
			['shipping_apiship_yandex_api_key', '', 0],
			['shipping_apiship_sort_order', '', 0],
			['shipping_apiship_mode', 'shipping_apiship_mode_normal', 0],
			['shipping_apiship_provider', '{"cdek":{"pickup_type":"1","id":"2295286"}}', 1],
			['shipping_apiship_mapping_status', '{"delivering":{"use":"1","order_status_id":"3","notify":"1"},"deliveryCanceled":{"use":"1","order_status_id":"7","notify":"1"},"readyForRecipient":{"use":"1","order_status_id":"5","notify":"1"},"uploaded":{"use":"1","order_status_id":"1","notify":"1"}}', 1],
			['shipping_apiship_paid_orders', '["5","2","17","7","3","1"]', 1],
			['shipping_apiship_crate_dimension_add', '10', 0],
			['shipping_apiship_crate_weight_add', '0', 0]
		];

		foreach ($apiship_settings as $setting) {
			$query = $this->db->query("SELECT setting_id FROM `" . DB_PREFIX . "setting` WHERE `store_id` = 0 AND `code` = 'shipping_apiship' AND `key` = '" . $this->db->escape($setting[0]) . "'");

			if (!$query->num_rows) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'shipping_apiship', '" . $this->db->escape($setting[0]) . "', '" . $this->db->escape($setting[1]) . "', " . (int)$setting[2] . ")");
			}
		}
	}

 	public function get_providers() {
		return $this->apiship->get_providers();
	}

 	public function get_providers_points() {
		return $this->apiship->get_providers_points();
	}

 	public function get_integrator_statuses() {
		return $this->apiship->get_integrator_statuses();
	}

	public function get_payment_methods() {

		$payment_methods = [];
		$this->load->model('setting/extension');
		$results = $this->model_setting_extension->getExtensionsByType('payment');

		foreach ($results as $result) {
			if (empty($result['code'])) {
				continue;
			}

			$extension = !empty($result['extension']) ? $result['extension'] : 'opencart';
			$result_code = $result['code'];

			$this->load->language('extension/' . $extension . '/payment/' . $result_code);
			$payment_methods[] = [
				'code' => $result_code,
				'name' => $this->language->get('heading_title')
			];
		}


		if (!$this->filterit) {
			$filterit_payments = isset($this->config->get('filterit_payment')['created']) ? $this->config->get('filterit_payment')['created'] : [];

			foreach ($filterit_payments as $code => $info) {
				$payment_methods[] = [
					'code' => $code,
					'name' => !empty($info['title'][$this->config->get('config_admin_language')]) ? '[' . $code . '] ' . $info['title'][$this->config->get('config_admin_language')] : '[' . $code . ']',
				];
			}
		}

		return $payment_methods;

	}



}