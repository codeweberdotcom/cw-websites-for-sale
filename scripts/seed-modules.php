<?php
/**
 * WP-CLI seed script: creates module_category terms and cw_module posts.
 *
 * Usage (from Plesk Terminal or SSH):
 *   sudo -u codeweber.ru_82h9v9srdrs /opt/plesk/php/8.2/bin/php /usr/local/bin/wp \
 *     eval-file wp-content/plugins/cw-websites-for-sale/scripts/seed-modules.php \
 *     --path=/var/www/vhosts/codeweber.ru/httpdocs
 */

defined( 'ABSPATH' ) || ( class_exists( 'WP_CLI' ) ?: die( 'Run via WP-CLI only.' ) );

$categories = [
	'popular'        => 'Популярные',
	'content'        => 'Контент',
	'trust'          => 'Доверие',
	'communications' => 'Коммуникации',
	'business'       => 'Бизнес',
	'shop'           => 'Магазин',
	'analytics'      => 'Аналитика',
	'integrations'   => 'Интеграции',
];

$term_ids = [];
foreach ( $categories as $slug => $name ) {
	$existing = get_term_by( 'slug', $slug, 'module_category' );
	if ( $existing ) {
		$term_ids[ $slug ] = $existing->term_id;
		WP_CLI::log( "  exists: {$name}" );
	} else {
		$r = wp_insert_term( $name, 'module_category', [ 'slug' => $slug ] );
		if ( is_wp_error( $r ) ) {
			WP_CLI::warning( "  failed: {$name} — " . $r->get_error_message() );
		} else {
			$term_ids[ $slug ] = $r['term_id'];
			WP_CLI::success( "  category: {$name}" );
		}
	}
}

// [ title, description, icon, color, [categories] ]
$modules = [
	[ 'Блог и публикации',              'Статьи, рубрики, авторы, теги, архивы и связанные материалы управляются из редактора.',                                     'edit',               '#3f78e0', [ 'popular', 'content' ] ],
	[ 'Портфолио и кейсы',              'Проекты, категории, галереи, состав решения и результаты публикуются как отдельные кейсы.',                                  'folder-open',        '#54a8c7', [ 'popular', 'content', 'trust' ] ],
	[ 'Услуги и направления',           'Услуги, категории, специалисты, цены и связанные формы обращения управляются централизованно.',                              'briefcase-alt',      '#747ed1', [ 'content', 'business' ] ],
	[ 'FAQ и база знаний',              'Вопросы распределяются по категориям, обновляются отдельно и получают FAQ-разметку.',                                        'question-circle',    '#f78b77', [ 'content' ] ],
	[ 'Отзывы и рейтинги',              'Клиенты отправляют отзывы через форму, а компания проверяет и публикует их после модерации.',                               'star',               '#45c4a0', [ 'trust' ] ],
	[ 'Команда и сотрудники',           'Отделы, должности, компетенции, контакты и персональные страницы ведутся из одной системы.',                                 'users-alt',          '#e2626b', [ 'trust', 'business' ] ],
	[ 'Клиенты и партнёры',             'Логотипы, категории компаний, описания и связи партнёров с проектами управляются отдельно.',                                 'handshake',          '#3f78e0', [ 'trust' ] ],
	[ 'Документы и таблицы',            'Документы, версии, файлы и интерактивные CSV/XLSX-таблицы публикуются без сборки страниц.',                                  'file-alt',           '#54a8c7', [ 'content' ] ],
	[ 'Формы и заявки',                 'Формы любой структуры, загрузка файлов, хранение обращений, UTM-данные и экспорт результатов.',                             'envelope-upload',    '#747ed1', [ 'popular', 'communications' ] ],
	[ 'Брифы и опросы',                 'Многошаговые анкеты, условные поля и загрузка материалов помогают собрать требования заранее.',                             'clipboard-alt',      '#f78b77', [ 'communications' ] ],
	[ 'Подписки и рассылки',            'Сбор подписчиков, подтверждение согласия, управление статусами, отписка, импорт и экспорт базы.',                           'envelope',           '#45c4a0', [ 'communications' ] ],
	[ 'Интеграция с Telegram',          'Заявки, регистрации и служебные сообщения поступают сотрудникам или в рабочую Telegram-группу.',                            'telegram',           '#e2626b', [ 'communications', 'integrations' ] ],
	[ 'Плавающий виджет мессенджеров',  'Быстрый переход в Telegram, WhatsApp, VK и другие каналы связи из любой части страницы.',                                   'share-alt',          '#3f78e0', [ 'popular', 'communications', 'integrations' ] ],
	[ 'Онлайн-чат',                     'Подключение JivoChat, Chatra, Битрикс24, Callibri или другого сервиса онлайн-консультаций.',                                'comment-alt-message','#54a8c7', [ 'popular', 'communications', 'integrations' ] ],
	[ 'Уведомления и объявления',       'Всплывающие сообщения, акции и предупреждения планируются на выбранные даты — на год вперёд.',                              'bell',               '#747ed1', [ 'communications', 'business' ] ],
	[ 'Вакансии и отклики',             'Вакансии, типы занятости, графики, сроки публикации и формы резюме управляются HR-командой.',                               'user-check',         '#f78b77', [ 'business' ] ],
	[ 'Мероприятия и регистрация',      'Календарь, программа, лимиты мест, регистрация, QR-билеты и добавление события в календарь.',                              'calendar-alt',       '#45c4a0', [ 'business' ] ],
	[ 'Филиалы и Яндекс Карты',         'Адреса, графики, контакты и сотрудники выводятся на карте через Яндекс Maps API 3.',                                        'map-marker',         '#e2626b', [ 'popular', 'business', 'integrations' ] ],
	[ 'Интернет-магазин',               'Каталог, карточки товаров, корзина, оформление заказа, доставка и кабинет покупателя.',                                     'shopping-cart',      '#3f78e0', [ 'shop' ] ],
	[ 'Фильтры товаров',                'Поиск по цене, категориям, характеристикам, рейтингу и наличию без перезагрузки каталога.',                                 'filter',             '#54a8c7', [ 'shop' ] ],
	[ 'Онлайн-оплата',                  'Подключение эквайринга и СБП через Сбер, Т-Банк, ПСБ, ЮKassa и другие сервисы.',                                           'credit-card',        '#747ed1', [ 'shop', 'integrations' ] ],
	[ 'Избранное и сравнение',          'Покупатели сохраняют товары и сопоставляют характеристики перед принятием решения.',                                        'heart',              '#f78b77', [ 'shop' ] ],
	[ 'Конфигуратор товара',            'Дополнительные опции, наборы параметров и зависимые варианты помогают собрать предложение.',                                'sliders-v-alt',      '#45c4a0', [ 'shop' ] ],
	[ 'Метрики и конверсии',            'Matomo отслеживает источники, посещения, открытия и отправки форм и ключевые действия.',                                    'chart-line',         '#e2626b', [ 'popular', 'analytics' ] ],
	[ 'Поиск и статистика запросов',    'AJAX-поиск показывает результаты сразу и сохраняет запросы, по которым ничего не найдено.',                                 'search',             '#3f78e0', [ 'analytics' ] ],
	[ 'Мультиязычная версия',           'Языковые версии страниц и интерфейса создаются с отдельными адресами и SEO-настройками.',                                   'globe',              '#54a8c7', [ 'integrations' ] ],
];

$created = 0;
foreach ( $modules as [ $title, $content, $icon, $color, $cats ] ) {
	$existing = get_posts( [ 'post_type' => 'cw_module', 'post_status' => 'any', 'title' => $title, 'numberposts' => 1 ] );
	if ( $existing ) {
		WP_CLI::log( "  skip (exists): {$title}" );
		continue;
	}

	$id = wp_insert_post( [
		'post_type'    => 'cw_module',
		'post_title'   => $title,
		'post_content' => $content,
		'post_status'  => 'publish',
	], true );

	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "  failed: {$title}" );
		continue;
	}

	update_post_meta( $id, '_module_icon',  $icon );
	update_post_meta( $id, '_module_color', $color );

	$ids = array_values( array_filter( array_map( fn( $s ) => $term_ids[ $s ] ?? null, $cats ) ) );
	if ( $ids ) {
		wp_set_post_terms( $id, $ids, 'module_category' );
	}

	WP_CLI::success( "  created: {$title} (#{$id})" );
	$created++;
}

WP_CLI::log( "\nDone. Created {$created} of " . count( $modules ) . " modules." );
