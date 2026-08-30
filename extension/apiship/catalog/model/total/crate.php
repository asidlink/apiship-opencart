<?php
namespace Opencart\Catalog\Model\Extension\Apiship\Total;
/**
 * Class Crate
 *
 * Отдельная строка «Обрешётка» в итогах заказа.
 * Стоимость берётся из карточки товара (crate_cost × количество);
 * если у товара не указана — из настройки модуля (₽ за единицу).
 *
 * @package Opencart\Catalog\Model\Extension\Apiship\Total
 */
class Crate extends \Opencart\System\Engine\Model {
	/**
	 * Get Total
	 *
	 * @param array<int, array<string, mixed>> $totals
	 * @param array<int, float>                $taxes
	 * @param float                            $total
	 *
	 * @return void
	 */
	public function getTotal(array &$totals, array &$taxes, float &$total): void {
		// Обрешётка добавляется в расчёт ТОЛЬКО когда покупатель выбрал
		// доставку (самовывоз по умолчанию). На странице корзины и при
		// самовывозе строка «Обрешётка» не показывается.
		if (empty($this->session->data['shipping_required'])) {
			return;
		}

		$crate_sum = 0.0;

		foreach ($this->cart->getProducts() as $product) {
			if (empty($product['crate'])) continue;

			$per_unit = isset($product['crate_cost']) ? (float)$product['crate_cost'] : 0.0;

			if ($per_unit <= 0) {
				$per_unit = (float)$this->config->get('shipping_apiship_crate_cost');
			}

			$crate_sum += $per_unit * (int)$product['quantity'];
		}

		if ($crate_sum > 0) {
			$title = $this->config->get('total_crate_title');

			if (!$title) {
				$title = 'Обрешётка';
			}

			// Копейки не нужны — округляем до целых рублей.
			$crate_value = round($crate_sum);

			$totals[] = [
				'extension'  => 'apiship',
				'code'       => 'crate',
				'title'      => $title,
				'value'      => $crate_value,
				'sort_order' => (int)$this->config->get('total_crate_sort_order')
			];

			$total += $crate_value;
		}
	}
}
