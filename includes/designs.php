<?php
/**
 * The six page designs: registry, CSS and markup.
 * Colours are driven by CSS variables: --bg (background), --ac (accent), --hi (highlight).
 *
 * @package ThankYouMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Design registry. colors = default [background, accent, highlight].
 */
function tym_designs() {
	return array(
		1 => array( 'name' => 'Vault Classic',      'desc' => 'Navy, gold and red glass card. Timeless and premium.', 'colors' => array( '#0E1530', '#D4AF37', '#ED1C24' ), 'font' => 'Cinzel:wght@600;700' ),
		2 => array( 'name' => 'Arcade Neon',        'desc' => 'Retro arcade look with neon glow and a blinking "insert coin" line.', 'colors' => array( '#07031a', '#19f6ff', '#ff2bd6' ), 'font' => 'Press+Start+2P' ),
		3 => array( 'name' => "Collector's Ticket", 'desc' => 'Cream background, big serif "Thank you." on an Admit One ticket stub.', 'colors' => array( '#F1EBDD', '#14161f', '#ED1C24' ), 'font' => 'Playfair+Display:wght@700;900' ),
		4 => array( 'name' => 'Comic Pop',          'desc' => 'Yellow halftone background, bold comic title and a speech bubble.', 'colors' => array( '#FFD60A', '#000000', '#ED1C24' ), 'font' => 'Bangers' ),
		5 => array( 'name' => 'Vegas Marquee',      'desc' => 'Glowing title inside blinking marquee bulbs - a casino-sign feel.', 'colors' => array( '#120405', '#ffd978', '#ff8a00' ), 'font' => 'Cinzel:wght@600;700' ),
		6 => array( 'name' => 'Minimal Editorial',  'desc' => 'Huge type on near-black with a gold period. Clean and modern.', 'colors' => array( '#0a0a0a', '#D4AF37', '#D4AF37' ), 'font' => 'Space+Grotesk:wght@500;700' ),
	);
}

/* ---------------------------------------------------------------
 * CSS
 * ------------------------------------------------------------- */
function tym_common_css() {
	return <<<'CSS'
*{box-sizing:border-box;margin:0;padding:0}
html,body{min-height:100%}
body{font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:var(--bg);color:#fff;-webkit-font-smoothing:antialiased}
img{max-width:100%;height:auto}
.opt{position:relative;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;overflow:hidden}
.btn{display:inline-block;margin-top:14px;padding:13px 28px;background:var(--hi);color:#fff;text-decoration:none;font-weight:600;letter-spacing:.06em;text-transform:uppercase;font-size:13px;border-radius:4px}
.btn:hover{filter:brightness(1.1)}
@media(prefers-reduced-motion:reduce){*{animation:none!important}}
CSS;
}

function tym_design_css( $d ) {
	switch ( (int) $d ) {
		case 2:
			return <<<'CSS'
.d2{background:linear-gradient(color-mix(in srgb,var(--ac) 10%,transparent) 1px,transparent 1px) 0 0/44px 44px,linear-gradient(90deg,color-mix(in srgb,var(--hi) 10%,transparent) 1px,transparent 1px) 0 0/44px 44px,radial-gradient(ellipse at 50% 120%,color-mix(in srgb,var(--hi) 30%,var(--bg)),var(--bg) 65%)}
.d2 .card{width:100%;max-width:760px;text-align:center;border:3px solid var(--ac);box-shadow:0 0 20px var(--ac),inset 0 0 24px color-mix(in srgb,var(--ac) 25%,transparent);background:color-mix(in srgb,var(--bg) 85%,transparent);padding:48px 28px;border-radius:6px}
.d2 .logo{width:200px;margin:0 auto 26px;display:block;filter:drop-shadow(0 0 10px var(--hi))}
.d2 .eyebrow{font-family:'Press Start 2P',monospace;font-size:11px;color:var(--ac);letter-spacing:.12em;margin-bottom:22px;text-shadow:0 0 8px var(--ac);text-transform:uppercase;line-height:1.6}
.d2 h1{font-family:'Press Start 2P',monospace;font-weight:400;font-size:clamp(28px,6.5vw,56px);line-height:1.25;color:#fff;text-transform:uppercase;text-shadow:0 0 8px var(--hi),0 0 22px var(--hi),0 0 44px var(--hi);margin-bottom:26px}
.d2 p{font-size:17px;line-height:1.7;color:#d9d3ff;margin-bottom:14px}
.d2 .coin{display:inline-block;margin-top:14px;font-family:'Press Start 2P',monospace;font-size:11px;line-height:1.7;color:#ffe600;text-shadow:0 0 8px #ffe600;animation:blink 1.1s steps(2) infinite}
.d2 .contact{margin-top:22px;font-size:13px;color:#9a94c9}
.d2 .contact a{color:var(--ac);text-decoration:none}
.d2 .btn{background:var(--hi);box-shadow:0 0 14px var(--hi)}
@keyframes blink{50%{opacity:.15}}
@media(max-width:600px){.d2 .card{padding:34px 18px}}
CSS;
		case 3:
			return <<<'CSS'
.d3{background:var(--bg)}
.d3 .ticket{width:100%;max-width:960px;display:grid;grid-template-columns:1fr 260px;background:#fff;border-radius:12px;box-shadow:0 20px 50px rgba(60,40,10,.25);position:relative}
.d3 .ticket.no-stub{grid-template-columns:1fr}
.d3 .main{padding:56px 48px;border-right:3px dashed #cfc5ae}
.d3 .no-stub .main{border-right:0}
.d3 .stub{background:var(--hi);color:#fff;border-radius:0 12px 12px 0;padding:32px 20px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:18px}
.d3 .ticket:before,.d3 .ticket:after{content:"";position:absolute;right:243px;width:34px;height:34px;border-radius:50%;background:var(--bg);z-index:2}
.d3 .no-stub:before,.d3 .no-stub:after{display:none}
.d3 .ticket:before{top:-17px}.d3 .ticket:after{bottom:-17px}
.d3 .eyebrow{font-size:12px;letter-spacing:.22em;text-transform:uppercase;color:var(--hi);font-weight:600;margin-bottom:12px}
.d3 h1{font-family:'Playfair Display',serif;font-weight:900;font-size:clamp(52px,9vw,104px);line-height:.95;color:var(--ac);letter-spacing:-.02em;margin-bottom:22px}
.d3 h1 i{color:var(--hi);font-style:normal}
.d3 p{font-size:18px;line-height:1.7;color:#3b3b46;margin-bottom:12px;max-width:520px}
.d3 .contact{margin-top:22px;font-size:14px;color:#6b6b78}
.d3 .contact a{color:var(--ac);font-weight:600}
.d3 .badge{background:#0E1530;border-radius:10px;padding:14px}
.d3 .badge img{width:150px;display:block}
.d3 .admit{font-weight:700;letter-spacing:.18em;font-size:13px;text-transform:uppercase}
.d3 .big{font-family:'Playfair Display',serif;font-size:34px;font-weight:900;line-height:1.05}
.d3 .btn{background:var(--ac)}
@media(max-width:760px){
 .d3 .ticket{grid-template-columns:1fr}
 .d3 .main{border-right:0;border-bottom:3px dashed #cfc5ae;padding:40px 26px}
 .d3 .no-stub .main{border-bottom:0}
 .d3 .stub{border-radius:0 0 12px 12px}
 .d3 .ticket:before,.d3 .ticket:after{display:none}
}
CSS;
		case 4:
			return <<<'CSS'
.d4{background-color:var(--bg);background-image:radial-gradient(rgba(0,0,0,.16) 2.4px,transparent 2.6px);background-size:20px 20px}
.d4 .wrap{width:100%;max-width:780px;text-align:center}
.d4 .badge{display:inline-block;background:#0E1530;border:4px solid var(--ac);border-radius:10px;padding:12px 16px;box-shadow:6px 6px 0 var(--ac);transform:rotate(-2deg);margin-bottom:24px}
.d4 .badge img{width:170px;display:block}
.d4 h1{font-family:'Bangers',cursive;font-weight:400;font-size:clamp(64px,13vw,140px);line-height:.95;letter-spacing:.03em;color:var(--hi);-webkit-text-stroke:4px var(--ac);paint-order:stroke fill;text-shadow:7px 7px 0 var(--ac);transform:rotate(-2deg);margin-bottom:30px}
.d4 .bubble{position:relative;background:#fff;border:5px solid var(--ac);border-radius:26px;padding:28px 30px;box-shadow:10px 10px 0 var(--ac);text-align:left;color:#111}
.d4 .bubble:after{content:"";position:absolute;left:60px;bottom:-34px;width:34px;height:34px;background:#fff;border-right:5px solid var(--ac);border-bottom:5px solid var(--ac);transform:skewX(-35deg)}
.d4 .bubble p{font-size:19px;line-height:1.55;font-weight:600;margin-bottom:12px}
.d4 .bubble p:last-child{margin-bottom:0}
.d4 .contact{margin-top:50px;font-weight:700;color:#111;font-size:15px}
.d4 .contact a{color:#111;background:#fff;border:3px solid var(--ac);padding:4px 10px;border-radius:8px;text-decoration:none;box-shadow:3px 3px 0 var(--ac)}
.d4 .btn{margin-top:34px;border:4px solid var(--ac);box-shadow:5px 5px 0 var(--ac);color:#fff;border-radius:10px;background:var(--hi)}
CSS;
		case 5:
			return <<<'CSS'
.d5{background:radial-gradient(ellipse at 50% 40%,color-mix(in srgb,var(--hi) 22%,var(--bg)),var(--bg) 70%)}
.d5 .marq{position:relative;width:100%;max-width:780px;text-align:center;padding:70px 46px;background:color-mix(in srgb,var(--bg) 70%,#000);border:3px solid var(--ac);border-radius:6px;box-shadow:0 0 50px color-mix(in srgb,var(--ac) 25%,transparent),inset 0 0 40px rgba(0,0,0,.8)}
.d5 .b{position:absolute;animation:twinkle 1.4s ease-in-out infinite alternate;filter:drop-shadow(0 0 4px var(--ac))}
.d5 .b.t,.d5 .b.bt{left:10px;right:10px;height:16px;background-image:radial-gradient(circle,var(--ac) 0 4.5px,transparent 5px);background-size:28px 16px;background-repeat:repeat-x}
.d5 .b.t{top:10px}.d5 .b.bt{bottom:10px;animation-delay:.7s}
.d5 .b.l,.d5 .b.r{top:10px;bottom:10px;width:16px;background-image:radial-gradient(circle,var(--ac) 0 4.5px,transparent 5px);background-size:16px 28px;background-repeat:repeat-y}
.d5 .b.l{left:10px;animation-delay:.35s}.d5 .b.r{right:10px;animation-delay:1s}
@keyframes twinkle{from{opacity:.45}to{opacity:1}}
.d5 .logo{width:200px;margin:0 auto 24px;display:block}
.d5 .eyebrow{font-family:'Cinzel',serif;color:var(--ac);letter-spacing:.3em;font-size:13px;text-transform:uppercase;margin-bottom:12px}
.d5 h1{font-family:'Cinzel',serif;font-size:clamp(44px,9vw,92px);line-height:1;color:#fff4cf;text-transform:uppercase;text-shadow:0 0 10px var(--hi),0 0 28px var(--hi),0 0 60px var(--hi);margin-bottom:22px}
.d5 .divider{color:var(--ac);letter-spacing:.6em;font-size:12px;margin-bottom:22px}
.d5 p{font-size:18px;line-height:1.7;color:#f2dfc0;margin-bottom:14px}
.d5 .contact{margin-top:22px;font-size:14px;color:#c9a97a}
.d5 .contact a{color:var(--ac);text-decoration:none}
.d5 .btn{background:var(--ac);color:#1b0709}
@media(max-width:600px){.d5 .marq{padding:56px 30px}}
CSS;
		case 6:
			return <<<'CSS'
.d6{background:var(--bg);justify-content:flex-start;padding:70px 6vw}
.d6 .grid{width:100%;max-width:1200px;margin:0 auto}
.d6 .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:9vh;gap:16px;flex-wrap:wrap}
.d6 .logo{width:150px;display:block}
.d6 .year{font-size:13px;letter-spacing:.25em;text-transform:uppercase;color:#888}
.d6 h1{font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:clamp(60px,14vw,180px);line-height:.92;letter-spacing:-.05em;color:#fff}
.d6 h1 span{color:var(--ac)}
.d6 .line{height:1px;background:linear-gradient(90deg,var(--ac),transparent);margin:5vh 0 4vh}
.d6 .cols{display:grid;grid-template-columns:1.4fr 1fr;gap:40px}
.d6 p{font-size:20px;line-height:1.6;color:#cfcfcf;max-width:560px;margin-bottom:12px}
.d6 .side{border-left:1px solid #2c2c2c;padding-left:28px;font-size:14px;color:#888;line-height:1.9}
.d6 .side b{display:block;color:var(--ac);letter-spacing:.2em;text-transform:uppercase;font-size:11px;margin-bottom:6px}
.d6 .side a{color:#fff;text-decoration:none}
.d6 .btn{background:var(--hi);color:#000}
@media(max-width:760px){.d6 .cols{grid-template-columns:1fr}.d6 .side{border-left:0;padding-left:0;border-top:1px solid #2c2c2c;padding-top:20px}}
CSS;
		case 1:
		default:
			return <<<'CSS'
.d1{background:radial-gradient(ellipse at 50% 0%,color-mix(in srgb,var(--ac) 16%,transparent),transparent 55%),radial-gradient(ellipse at 50% 100%,color-mix(in srgb,var(--hi) 14%,transparent),transparent 55%),var(--bg)}
.d1 .card{width:100%;max-width:720px;text-align:center;background:color-mix(in srgb,var(--bg) 75%,transparent);backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);border:1px solid color-mix(in srgb,var(--hi) 35%,transparent);border-top:1px solid var(--ac);border-radius:10px;padding:48px 32px;box-shadow:0 12px 40px rgba(0,0,0,.6),0 0 40px color-mix(in srgb,var(--hi) 12%,transparent)}
.d1 .logo{width:220px;margin:0 auto 26px;display:block}
.d1 .eyebrow{font-size:12px;letter-spacing:.2em;text-transform:uppercase;color:var(--ac);margin-bottom:14px}
.d1 h1{font-family:'Cinzel',serif;font-size:clamp(38px,7vw,60px);text-transform:uppercase;line-height:1.1}
.d1 .rule{width:80px;height:3px;background:var(--hi);box-shadow:0 0 12px var(--hi);margin:22px auto}
.d1 p{font-size:18px;line-height:1.7;color:#E2E2EC;margin-bottom:14px}
.d1 .contact{margin-top:24px;font-size:14px;color:#A9A9BC}
.d1 .contact a{color:var(--ac);text-decoration:none}
@media(max-width:600px){.d1 .card{padding:34px 18px}}
CSS;
	}
}

/* ---------------------------------------------------------------
 * Markup
 * ------------------------------------------------------------- */
function tym_design_body( $d, $o, $paras ) {
	$logo     = ! empty( $o['logo'] ) ? '<img class="logo" src="' . esc_url( $o['logo'] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">' : '';
	$logo_raw = ! empty( $o['logo'] ) ? '<img src="' . esc_url( $o['logo'] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">' : '';
	$eyebrow  = ! empty( $o['eyebrow'] ) ? esc_html( $o['eyebrow'] ) : '';
	$headline = trim( (string) $o['headline'] );
	$h_plain  = rtrim( $headline, '.! ' );
	$h_lines  = str_replace( ' ', '<br>', esc_html( $h_plain ) );  // one word per line (designs 3 & 6)
	$h_lines2 = str_replace( ' ', '<br>', esc_html( $headline ) ); // keeps punctuation (design 2)

	$p_html = '';
	foreach ( $paras as $para ) {
		$p_html .= '<p>' . wp_kses_post( $para ) . '</p>';
	}

	$btn = '';
	if ( ! empty( $o['button_text'] ) && ! empty( $o['button_url'] ) ) {
		$btn = '<a class="btn" href="' . esc_url( $o['button_url'] ) . '">' . esc_html( $o['button_text'] ) . '</a>';
	}

	$label   = trim( (string) $o['contact_label'] );
	$contact = '';
	$email   = '';
	if ( ! empty( $o['email'] ) && is_email( $o['email'] ) ) {
		$email   = '<a href="mailto:' . esc_attr( $o['email'] ) . '">' . esc_html( $o['email'] ) . '</a>';
		$contact = '<div class="contact">' . ( '' !== $label ? esc_html( $label ) . ' ' : '' ) . $email . '</div>';
	}

	$tagline    = trim( (string) $o['tagline'] );
	$stub_label = trim( (string) $o['stub_label'] );
	$stub_text  = trim( (string) $o['stub_text'] );
	$has_stub   = ( $logo_raw || '' !== $stub_label || '' !== $stub_text );

	ob_start();
	switch ( (int) $d ) {
		case 2:
			?>
<div class="opt d2"><div class="card">
	<?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php if ( $eyebrow ) : ?><div class="eyebrow"><?php echo $eyebrow; // phpcs:ignore ?></div><?php endif; ?>
	<h1><?php echo $h_lines2; // phpcs:ignore ?></h1>
	<?php echo $p_html; // phpcs:ignore ?>
	<?php if ( '' !== $tagline ) : ?><div class="coin">&#9654; <?php echo esc_html( $tagline ); ?></div><br><?php endif; ?>
	<?php echo $btn; // phpcs:ignore ?>
	<?php echo $contact; // phpcs:ignore ?>
</div></div>
			<?php
			break;
		case 3:
			?>
<div class="opt d3"><div class="ticket<?php echo $has_stub ? '' : ' no-stub'; ?>">
	<div class="main">
		<?php if ( $eyebrow ) : ?><div class="eyebrow"><?php echo $eyebrow; // phpcs:ignore ?></div><?php endif; ?>
		<h1><?php echo $h_lines; // phpcs:ignore ?><i>.</i></h1>
		<?php echo $p_html; // phpcs:ignore ?>
		<?php echo $btn; // phpcs:ignore ?>
		<?php echo $contact; // phpcs:ignore ?>
	</div>
	<?php if ( $has_stub ) : ?>
	<div class="stub">
		<?php if ( $logo_raw ) : ?><div class="badge"><?php echo $logo_raw; // phpcs:ignore ?></div><?php endif; ?>
		<?php if ( '' !== $stub_label ) : ?><div class="admit"><?php echo esc_html( $stub_label ); ?></div><?php endif; ?>
		<?php if ( '' !== $stub_text ) : ?><div class="big"><?php echo esc_html( $stub_text ); ?></div><?php endif; ?>
	</div>
	<?php endif; ?>
</div></div>
			<?php
			break;
		case 4:
			?>
<div class="opt d4"><div class="wrap">
	<?php if ( $logo_raw ) : ?><div class="badge"><?php echo $logo_raw; // phpcs:ignore ?></div><?php endif; ?>
	<h1><?php echo esc_html( $headline ); ?></h1>
	<div class="bubble"><?php echo $p_html; // phpcs:ignore ?></div>
	<?php echo $btn; // phpcs:ignore ?>
	<?php echo $contact; // phpcs:ignore ?>
</div></div>
			<?php
			break;
		case 5:
			?>
<div class="opt d5"><div class="marq">
	<span class="b t"></span><span class="b bt"></span><span class="b l"></span><span class="b r"></span>
	<?php echo $logo; // phpcs:ignore ?>
	<?php if ( $eyebrow ) : ?><div class="eyebrow"><?php echo $eyebrow; // phpcs:ignore ?></div><?php endif; ?>
	<h1><?php echo esc_html( $h_plain ); ?></h1>
	<div class="divider">&#9733; &#9733; &#9733;</div>
	<?php echo $p_html; // phpcs:ignore ?>
	<?php echo $btn; // phpcs:ignore ?>
	<?php echo $contact; // phpcs:ignore ?>
</div></div>
			<?php
			break;
		case 6:
			?>
<div class="opt d6"><div class="grid">
	<div class="top">
		<?php echo $logo; // phpcs:ignore ?>
		<?php if ( $eyebrow ) : ?><div class="year"><?php echo $eyebrow; // phpcs:ignore ?></div><?php endif; ?>
	</div>
	<h1><?php echo $h_lines; // phpcs:ignore ?><span>.</span></h1>
	<div class="line"></div>
	<div class="cols">
		<div><?php echo $p_html; // phpcs:ignore ?><?php echo $btn; // phpcs:ignore ?></div>
		<?php if ( $email ) : ?><div class="side"><?php if ( '' !== $label ) : ?><b><?php echo esc_html( rtrim( $label, '? ' ) ); ?></b><?php endif; ?><?php echo $email; // phpcs:ignore ?></div><?php endif; ?>
	</div>
</div></div>
			<?php
			break;
		case 1:
		default:
			?>
<div class="opt d1"><div class="card">
	<?php echo $logo; // phpcs:ignore ?>
	<?php if ( $eyebrow ) : ?><div class="eyebrow"><?php echo $eyebrow; // phpcs:ignore ?></div><?php endif; ?>
	<h1><?php echo esc_html( $headline ); ?></h1>
	<div class="rule"></div>
	<?php echo $p_html; // phpcs:ignore ?>
	<?php echo $btn; // phpcs:ignore ?>
	<?php echo $contact; // phpcs:ignore ?>
</div></div>
			<?php
	}
	return ob_get_clean();
}
