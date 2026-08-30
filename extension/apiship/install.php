<?php
/**
 * Установка/удаление модуля доставки ApiShip для OpenCart 4.1.0.3.
 *
 * OpenCart вызывает эти функции при установке/удалении расширения.
 * Реальную работу — создание таблиц `oc_apiship_order` и `oc_apiship_order_status`,
 * регистрацию модуля доставки, расширения итогов «Обрешётка» и восстановление
 * настроек — выполняет admin controller `install()`/`uninstall()`
 * (путь `extension/apiship/shipping/apiship.install` / `.uninstall`).
 */
function install() {}
function uninstall() {}
