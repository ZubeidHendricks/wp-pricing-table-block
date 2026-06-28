<?php
/**
 * Plugin Name:       Pricing Table
 * Plugin URI:        https://zubeidhendricks.dev/wp-plugins/pricing-table-block
 * Description:        Build clean, responsive pricing tables with simple shortcodes — highlight a plan, add features and a call-to-action.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.2
 * Author:            Zubeid Hendricks
 * Author URI:        https://zubeidhendricks.dev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pricing-table-block
 *
 * @package PricingTableBlock
 */

defined( 'ABSPATH' ) || exit;

define( 'PRICING_TABLE_BLOCK_VERSION', '1.0.0' );

require_once __DIR__ . '/includes/factory-core.php';

/**
 * Pricing Table.
 */
final class PricingTableBlock extends ZubFactory_Plugin {

	private $styled = false;

	protected function configure() {
		$this->slug    = 'pricing-table-block';
		$this->title   = 'Pricing Table';
		$this->version = PRICING_TABLE_BLOCK_VERSION;
	}

	protected function settings_fields() {
		return array(
			'accent' => array(
				'label'   => __( 'Accent colour', 'pricing-table-block' ),
				'type'    => 'color',
				'default' => '#2271b1',
			),
		);
	}

	protected function hooks() {
		add_shortcode( 'pricing', array( $this, 'wrap' ) );
		add_shortcode( 'plan', array( $this, 'plan' ) );
	}

	/** [pricing] ... [/pricing] */
	public function wrap( $atts, $content = '' ) {
		ob_start();
		if ( ! $this->styled ) {
			$this->styled = true;
			$this->styles();
		}
		echo '<div class="zpt">' . do_shortcode( (string) $content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		return ob_get_clean();
	}

	/**
	 * [plan name="Pro" price="$29" period="/mo" url="#" cta="Buy"
	 *        features="Feature one|Feature two|Feature three" featured="yes"]
	 */
	public function plan( $atts ) {
		$atts = shortcode_atts(
			array(
				'name'     => '',
				'price'    => '',
				'period'   => '',
				'url'      => '#',
				'cta'      => __( 'Choose plan', 'pricing-table-block' ),
				'features' => '',
				'featured' => 'no',
			),
			$atts,
			'plan'
		);

		$featured = in_array( strtolower( $atts['featured'] ), array( 'yes', 'true', '1' ), true );
		$features = array_filter( array_map( 'trim', explode( '|', (string) $atts['features'] ) ) );

		ob_start();
		?>
		<div class="zpt-card <?php echo $featured ? 'zpt-featured' : ''; ?>">
			<?php if ( $featured ) : ?>
				<div class="zpt-tag"><?php esc_html_e( 'Most popular', 'pricing-table-block' ); ?></div>
			<?php endif; ?>
			<h3 class="zpt-name"><?php echo esc_html( $atts['name'] ); ?></h3>
			<div class="zpt-price">
				<span class="zpt-amt"><?php echo esc_html( $atts['price'] ); ?></span>
				<span class="zpt-per"><?php echo esc_html( $atts['period'] ); ?></span>
			</div>
			<?php if ( $features ) : ?>
				<ul class="zpt-feats">
					<?php foreach ( $features as $f ) : ?>
						<li><?php echo esc_html( $f ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<a class="zpt-cta" href="<?php echo esc_url( $atts['url'] ); ?>"><?php echo esc_html( $atts['cta'] ); ?></a>
		</div>
		<?php
		return ob_get_clean();
	}

	private function styles() {
		$accent = $this->option( 'accent', '#2271b1' ) ?: '#2271b1';
		?>
		<style>
			.zpt{display:flex;gap:20px;flex-wrap:wrap;justify-content:center;margin:20px 0;font-family:inherit}
			.zpt-card{flex:1 1 240px;max-width:320px;border:1px solid #e2e8f0;border-radius:14px;
				padding:28px 24px;text-align:center;background:#fff;position:relative}
			.zpt-featured{border-color:<?php echo esc_attr( $accent ); ?>;box-shadow:0 8px 28px rgba(0,0,0,.1);transform:translateY(-4px)}
			.zpt-tag{position:absolute;top:-12px;left:50%;transform:translateX(-50%);
				background:<?php echo esc_attr( $accent ); ?>;color:#fff;font-size:11px;font-weight:700;
				letter-spacing:.5px;text-transform:uppercase;padding:4px 12px;border-radius:20px}
			.zpt-name{margin:0 0 10px;font-size:20px}
			.zpt-price{margin:0 0 18px}
			.zpt-amt{font-size:40px;font-weight:800}
			.zpt-per{opacity:.6;font-size:15px}
			.zpt-feats{list-style:none;padding:0;margin:0 0 22px;text-align:left}
			.zpt-feats li{padding:8px 0 8px 26px;position:relative;border-bottom:1px solid #f1f5f9}
			.zpt-feats li::before{content:"\2713";position:absolute;left:0;color:<?php echo esc_attr( $accent ); ?>;font-weight:700}
			.zpt-cta{display:block;background:<?php echo esc_attr( $accent ); ?>;color:#fff;
				text-decoration:none;padding:12px;border-radius:8px;font-weight:600}
			.zpt-cta:hover{opacity:.92}
		</style>
		<?php
	}
}

add_action(
	'plugins_loaded',
	function () {
		( new PricingTableBlock( __FILE__ ) )->boot();
	}
);
