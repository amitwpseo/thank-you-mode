<?php
/**
 * Admin: settings page, assets, status indicators.
 *
 * @package ThankYouMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	add_options_page( 'Thank You Mode', 'Thank You Mode', 'manage_options', 'thank-you-mode', 'tym_settings_page' );
} );

add_filter( 'plugin_action_links_' . plugin_basename( TYM_FILE ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=thank-you-mode' ) ) . '">Settings</a>' );
	return $links;
} );

add_action( 'admin_init', function () {
	register_setting( 'tym_group', TYM_OPTION, array( 'sanitize_callback' => 'tym_sanitize' ) );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'settings_page_thank-you-mode' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'tym-admin', TYM_URL . 'assets/admin.css', array(), TYM_VERSION );
	wp_enqueue_script( 'tym-admin', TYM_URL . 'assets/admin.js', array( 'jquery', 'wp-color-picker' ), TYM_VERSION, true );
	wp_localize_script( 'tym-admin', 'TYM', array(
		'previewUrl' => add_query_arg( 'tym_preview', '1', home_url( '/' ) ),
	) );
} );

/** Human-readable status for the header pill. */
function tym_status_label( $o ) {
	$state = tym_state( $o );
	$fmt   = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	switch ( $state ) {
		case 'live':
			return array( 'live', 'ON - visitors see the Thank You page' );
		case 'scheduled':
			return array( 'scheduled', 'SCHEDULED - starts ' . wp_date( $fmt, tym_ts( $o['start_at'] ) ) );
		case 'ended':
			return array( 'off', 'ENDED - site is public again' );
		default:
			return array( 'off', 'OFF - site is public' );
	}
}

function tym_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$o       = tym_get();
	$n       = TYM_OPTION;
	$designs = tym_designs();
	$cur     = isset( $designs[ (int) $o['design'] ] ) ? (int) $o['design'] : 1;
	list( $pill_class, $pill_text ) = tym_status_label( $o );
	$preview_url = add_query_arg( array( 'tym_preview' => '1', 'tym_design' => $cur ), home_url( '/' ) );
	?>
	<div class="wrap tym-wrap">
	<h1 class="screen-reader-text">Thank You Mode</h1>
	<form method="post" action="options.php" id="tym-form">
		<?php settings_fields( 'tym_group' ); ?>

		<div class="tym-top">
			<div class="tym-brand">
				<div class="tym-mark">&#10084;</div>
				<div>
					<div class="tym-title">Thank You Mode <span class="tym-ver">v<?php echo esc_html( TYM_VERSION ); ?></span></div>
					<div class="tym-pill tym-pill-<?php echo esc_attr( $pill_class ); ?>"><i></i><?php echo esc_html( $pill_text ); ?></div>
				</div>
			</div>
			<div class="tym-actions">
				<label class="tym-switch" title="Turn Thank You Mode on or off">
					<input type="checkbox" name="<?php echo esc_attr( $n ); ?>[enabled]" value="1" <?php checked( $o['enabled'], 1 ); ?>>
					<span class="tym-slider"></span>
					<span class="tym-switch-label">Enabled</span>
				</label>
				<?php submit_button( 'Save Changes', 'primary large', 'submit', false ); ?>
			</div>
		</div>

		<nav class="tym-tabs" role="tablist">
			<a href="#design" data-tab="design" class="is-active">Design</a>
			<a href="#content" data-tab="content">Content</a>
			<a href="#rules" data-tab="rules">Visibility &amp; Rules</a>
			<a href="#advanced" data-tab="advanced">Advanced</a>
		</nav>

		<div class="tym-layout">
		<div class="tym-main">

			<!-- ================= DESIGN ================= -->
			<section class="tym-panel is-active" data-panel="design">
				<div class="tym-card-box">
					<h2>Choose a design</h2>
					<p class="description">Pick a style - the live preview on the right updates instantly. Press Save to publish it.</p>
					<div class="tym-grid">
						<?php foreach ( $designs as $id => $dd ) : ?>
							<label class="tym-opt <?php echo $id === $cur ? 'is-sel' : ''; ?>">
								<input type="radio" name="<?php echo esc_attr( $n ); ?>[design]" value="<?php echo (int) $id; ?>" <?php checked( $cur, $id ); ?>>
								<span class="tym-sw"><?php foreach ( $dd['colors'] as $col ) : ?><i style="background:<?php echo esc_attr( $col ); ?>"></i><?php endforeach; ?></span>
								<strong><?php echo (int) $id; ?>. <?php echo esc_html( $dd['name'] ); ?></strong>
								<small><?php echo esc_html( $dd['desc'] ); ?></small>
							</label>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="tym-card-box">
					<h2>Colors</h2>
					<p class="description">Optional. Leave a color empty (press <em>Clear</em>) to use the selected design's own color. Colors apply to the selected design - clear them if you switch designs.</p>
					<div class="tym-colors">
						<div><label>Background</label><input type="text" class="tym-color" name="<?php echo esc_attr( $n ); ?>[color_bg]" value="<?php echo esc_attr( $o['color_bg'] ); ?>"></div>
						<div><label>Accent <span class="description">(gold / cyan / outlines)</span></label><input type="text" class="tym-color" name="<?php echo esc_attr( $n ); ?>[color_ac]" value="<?php echo esc_attr( $o['color_ac'] ); ?>"></div>
						<div><label>Highlight <span class="description">(red / magenta / buttons)</span></label><input type="text" class="tym-color" name="<?php echo esc_attr( $n ); ?>[color_hi]" value="<?php echo esc_attr( $o['color_hi'] ); ?>"></div>
					</div>
				</div>

				<div class="tym-card-box">
					<h2>Background image <span class="tym-badge">New</span></h2>
					<p class="description">Optional photo behind the design (an event photo, your venue, a product shot). A dark overlay keeps the text readable.</p>
					<div class="tym-media">
						<button type="button" class="button tym-media-pick" data-target="#tym-bg" data-prev="#tym-bg-prev" data-title="Choose background image">Browse Media Library</button>
						<button type="button" class="button-link tym-media-clear" data-target="#tym-bg" data-prev="#tym-bg-prev">Remove</button>
					</div>
					<img id="tym-bg-prev" class="tym-prev" src="<?php echo esc_url( $o['bg_image'] ); ?>" alt="" style="display:<?php echo empty( $o['bg_image'] ) ? 'none' : 'block'; ?>">
					<input type="url" class="large-text" id="tym-bg" name="<?php echo esc_attr( $n ); ?>[bg_image]" value="<?php echo esc_attr( $o['bg_image'] ); ?>" placeholder="Or paste an image URL">
					<p style="margin-top:12px"><label>Overlay darkness: <input type="number" min="0" max="90" step="5" style="width:70px" name="<?php echo esc_attr( $n ); ?>[bg_overlay]" value="<?php echo (int) $o['bg_overlay']; ?>"> %</label></p>
				</div>
			</section>

			<!-- ================= CONTENT ================= -->
			<section class="tym-panel" data-panel="content">
				<div class="tym-card-box">
					<h2>Logo</h2>
					<div class="tym-media">
						<button type="button" class="button tym-media-pick" data-target="#tym-logo" data-prev="#tym-logo-prev" data-title="Choose logo">Browse Media Library</button>
						<button type="button" class="button-link tym-media-clear" data-target="#tym-logo" data-prev="#tym-logo-prev">Remove logo</button>
					</div>
					<img id="tym-logo-prev" class="tym-prev tym-prev-dark" src="<?php echo esc_url( $o['logo'] ); ?>" alt="" style="display:<?php echo empty( $o['logo'] ) ? 'none' : 'block'; ?>">
					<input type="url" class="large-text" id="tym-logo" name="<?php echo esc_attr( $n ); ?>[logo]" value="<?php echo esc_attr( $o['logo'] ); ?>" placeholder="Or paste an image URL">
				</div>

				<div class="tym-card-box">
					<h2>Text</h2>
					<table class="form-table" role="presentation">
						<tr><th><label>Small heading</label></th>
							<td><input type="text" class="regular-text" name="<?php echo esc_attr( $n ); ?>[eyebrow]" value="<?php echo esc_attr( $o['eyebrow'] ); ?>" placeholder="e.g. Your Event 2026"></td></tr>
						<tr><th><label>Main headline</label></th>
							<td><input type="text" class="large-text" name="<?php echo esc_attr( $n ); ?>[headline]" value="<?php echo esc_attr( $o['headline'] ); ?>"></td></tr>
						<tr><th><label>Message</label></th>
							<td><textarea class="large-text" rows="6" name="<?php echo esc_attr( $n ); ?>[message]"><?php echo esc_textarea( $o['message'] ); ?></textarea><p class="description">Each new line becomes a paragraph.</p></td></tr>
						<tr><th><label>Contact</label></th>
							<td>
								<input type="text" class="regular-text" placeholder="Label, e.g. Questions?" name="<?php echo esc_attr( $n ); ?>[contact_label]" value="<?php echo esc_attr( $o['contact_label'] ); ?>">
								<input type="email" class="regular-text" placeholder="email@example.com" name="<?php echo esc_attr( $n ); ?>[email]" value="<?php echo esc_attr( $o['email'] ); ?>">
								<p class="description">The email is only shown if you enter one.</p>
							</td></tr>
						<tr><th><label>Button (optional)</label></th>
							<td>
								<input type="text" class="regular-text" placeholder="Button text, e.g. Follow us" name="<?php echo esc_attr( $n ); ?>[button_text]" value="<?php echo esc_attr( $o['button_text'] ); ?>"><br><br>
								<input type="url" class="large-text" placeholder="https://..." name="<?php echo esc_attr( $n ); ?>[button_url]" value="<?php echo esc_attr( $o['button_url'] ); ?>">
							</td></tr>
					</table>
				</div>

				<div class="tym-card-box">
					<h2>Design-specific text</h2>
					<table class="form-table" role="presentation">
						<tr><th><label>Blinking line</label></th>
							<td><input type="text" class="large-text" name="<?php echo esc_attr( $n ); ?>[tagline]" value="<?php echo esc_attr( $o['tagline'] ); ?>">
							<p class="description">Shown on <strong>2. Arcade Neon</strong> with a &#9654; arrow. Leave empty to hide.</p></td></tr>
						<tr><th><label>Ticket stub text</label></th>
							<td>
								<input type="text" class="regular-text" placeholder="Admit One" name="<?php echo esc_attr( $n ); ?>[stub_label]" value="<?php echo esc_attr( $o['stub_label'] ); ?>">
								<input type="text" class="regular-text" placeholder="See you next time" name="<?php echo esc_attr( $n ); ?>[stub_text]" value="<?php echo esc_attr( $o['stub_text'] ); ?>">
								<p class="description">Shown on the stub of <strong>3. Collector's Ticket</strong>. Leave both empty (and no logo) to hide the stub.</p>
							</td></tr>
					</table>
				</div>
			</section>

			<!-- ================= RULES ================= -->
			<section class="tym-panel" data-panel="rules">
				<div class="tym-card-box">
					<h2>Schedule <span class="tym-badge">New</span></h2>
					<p class="description">Optional. Turn the Thank You page on and off automatically (uses your site's timezone: <?php echo esc_html( wp_timezone_string() ); ?>). Leave both empty to control it only with the switch at the top.</p>
					<p><label>Start on <input type="datetime-local" name="<?php echo esc_attr( $n ); ?>[start_at]" value="<?php echo esc_attr( $o['start_at'] ); ?>"></label>
					&nbsp;&nbsp; <label>End on <input type="datetime-local" name="<?php echo esc_attr( $n ); ?>[end_at]" value="<?php echo esc_attr( $o['end_at'] ); ?>"></label></p>
				</div>

				<div class="tym-card-box">
					<h2>How visitors are handled</h2>
					<table class="form-table" role="presentation">
						<tr><th><label>Other pages</label></th>
							<td>
								<select name="<?php echo esc_attr( $n ); ?>[other_urls]">
									<option value="redirect" <?php selected( $o['other_urls'], 'redirect' ); ?>>Redirect to the front page (recommended)</option>
									<option value="serve" <?php selected( $o['other_urls'], 'serve' ); ?>>Show the Thank You page on the same URL</option>
								</select>
							</td></tr>
						<tr><th><label>Response code</label></th>
							<td>
								<select name="<?php echo esc_attr( $n ); ?>[status]">
									<option value="200" <?php selected( (int) $o['status'], 200 ); ?>>200 OK - normal page (best for a Thank You page)</option>
									<option value="503" <?php selected( (int) $o['status'], 503 ); ?>>503 Temporarily unavailable (best for short maintenance)</option>
								</select>
								<p class="description">Use 503 only if the site will be back soon - it tells Google not to drop your pages.</p>
							</td></tr>
						<tr><th><label>Always-public pages</label></th>
							<td><textarea class="large-text" rows="4" name="<?php echo esc_attr( $n ); ?>[allow_paths]" placeholder="/privacy-policy/&#10;/contact/"><?php echo esc_textarea( $o['allow_paths'] ); ?></textarea>
							<p class="description">One path per line. These pages (and anything under them) stay visible to everyone.</p></td></tr>
					</table>
				</div>

				<div class="tym-card-box">
					<h2>Who still sees the real site <span class="tym-badge">New</span></h2>
					<table class="form-table" role="presentation">
						<tr><th>Administrators</th><td>Always see the full site while logged in. (Add <code>?tym_preview=1</code> to the address to see what visitors see.)</td></tr>
						<tr><th>Other logged-in users</th>
							<td><label><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[bypass_loggedin]" value="1" <?php checked( $o['bypass_loggedin'], 1 ); ?>> Let every logged-in user (editors, customers, members) see the real site</label></td></tr>
						<tr><th><label>Allowed IP addresses</label></th>
							<td><textarea class="large-text" rows="3" name="<?php echo esc_attr( $n ); ?>[allow_ips]" placeholder="203.0.113.10"><?php echo esc_textarea( $o['allow_ips'] ); ?></textarea>
							<p class="description">One IP per line - for your team or clients. Your current IP: <code><?php echo esc_html( tym_client_ip() ); ?></code>. Behind a CDN this may show the CDN's address.</p></td></tr>
					</table>
				</div>

				<div class="tym-card-box">
					<h2>Security &amp; SEO</h2>
					<p><label><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[block_rest]" value="1" <?php checked( $o['block_rest'], 1 ); ?>> Block the REST API and XML-RPC for logged-out visitors <span class="description">(stops page data being read behind the scenes; turn off if a form or app plugin needs it)</span></label></p>
					<p><label><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[noindex]" value="1" <?php checked( $o['noindex'], 1 ); ?>> Ask search engines not to index the page (noindex) <span class="description">(leave off if you want the Thank You page to be findable)</span></label></p>
				</div>
			</section>

			<!-- ================= ADVANCED ================= -->
			<section class="tym-panel" data-panel="advanced">
				<div class="tym-card-box">
					<table class="form-table" role="presentation">
						<tr><th><label>Browser tab title</label></th>
							<td><input type="text" class="large-text" name="<?php echo esc_attr( $n ); ?>[page_title]" value="<?php echo esc_attr( $o['page_title'] ); ?>"></td></tr>
						<tr><th>Fonts</th>
							<td><label><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[load_fonts]" value="1" <?php checked( $o['load_fonts'], 1 ); ?>> Load the design fonts from Google Fonts</label>
							<p class="description">Turn off for privacy (GDPR) or maximum speed - the page then uses system fonts.</p></td></tr>
						<tr><th><label>Custom CSS</label></th>
							<td><textarea class="large-text code" rows="8" name="<?php echo esc_attr( $n ); ?>[custom_css]" placeholder=".opt h1 { letter-spacing: .02em; }"><?php echo esc_textarea( $o['custom_css'] ); ?></textarea>
							<p class="description">Runs after the design styles. Example selectors: <code>.opt</code>, <code>.opt h1</code>, <code>.opt p</code>, <code>.btn</code>.</p></td></tr>
					</table>
				</div>
				<div class="tym-card-box">
					<h2>Developers</h2>
					<p class="description">Filters: <code>tym_settings</code> (modify settings), <code>tym_is_active</code> (override whether the mode is on). Uninstalling the plugin removes its settings; your pages and posts are never touched.</p>
				</div>
			</section>

		</div><!-- /main -->

		<aside class="tym-side">
			<div class="tym-preview">
				<div class="tym-pv-bar">
					<strong>Live preview</strong>
					<span class="tym-pv-tools">
						<button type="button" class="button button-small is-on" data-w="100%">Desktop</button>
						<button type="button" class="button button-small" data-w="390px">Mobile</button>
						<button type="button" class="button button-small" id="tym-refresh">Refresh</button>
						<a class="button button-small" id="tym-open" href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener">Open &rarr;</a>
					</span>
				</div>
				<div class="tym-frame-wrap"><iframe id="tym-frame" title="Preview" src="<?php echo esc_url( $preview_url ); ?>"></iframe></div>
				<p class="description">The preview shows your <strong>saved</strong> text, colors and logo. Press Save, then Refresh, to see edits.</p>
			</div>
		</aside>
		</div><!-- /layout -->
	</form>
	</div>
	<?php
}

/* ---------------------------------------------------------------
 * Notices & admin bar
 * ------------------------------------------------------------- */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) || ! tym_is_active() ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>Thank You Mode is ON.</strong> The public sees only the Thank You page. <a href="' . esc_url( admin_url( 'options-general.php?page=thank-you-mode' ) ) . '">Manage</a></p></div>';
} );

add_action( 'admin_bar_menu', function ( $bar ) {
	if ( ! current_user_can( 'manage_options' ) || ! tym_is_active() ) {
		return;
	}
	$bar->add_node( array(
		'id'    => 'tym-status',
		'title' => 'Thank You Mode: ON',
		'href'  => admin_url( 'options-general.php?page=thank-you-mode' ),
		'meta'  => array( 'class' => 'tym-on' ),
	) );
}, 100 );

add_action( 'admin_head', 'tym_bar_css' );
add_action( 'wp_head', 'tym_bar_css' );
function tym_bar_css() {
	if ( is_admin_bar_showing() ) {
		echo '<style>#wpadminbar .tym-on>.ab-item{background:#d63638!important;color:#fff!important}</style>';
	}
}
