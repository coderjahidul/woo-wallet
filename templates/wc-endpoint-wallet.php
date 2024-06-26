<?php
/**
 * The Template for displaying wallet recharge form
 *
 * This template can be overridden by copying it to yourtheme/woo-wallet/wc-endpoint-wallet.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author  Subrata Mal
 * @version     1.1.8
 * @package WooWallet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp;
do_action( 'woo_wallet_before_my_wallet_content' );
$is_rendred_from_myaccount = wc_post_content_has_shortcode( 'woo-wallet' ) ? false : is_account_page();
$menu_items                = apply_filters(
	'woo_wallet_nav_menu_items',
	array(
		'top_up'              => array(
			'title' => apply_filters( 'woo_wallet_account_topup_menu_title', __( 'Wallet topup', 'woo-wallet' ) ),
			'url'   => $is_rendred_from_myaccount ? esc_url( wc_get_endpoint_url( get_option( 'woocommerce_woo_wallet_endpoint', 'woo-wallet' ), 'add', wc_get_page_permalink( 'myaccount' ) ) ) : add_query_arg( 'wallet_action', 'add' ),
			'icon'  => 'dashicons dashicons-plus-alt',
		),
		'transfer'            => array(
			'title' => apply_filters( 'woo_wallet_account_transfer_amount_menu_title', __( 'Wallet transfer', 'woo-wallet' ) ),
			'url'   => $is_rendred_from_myaccount ? esc_url( wc_get_endpoint_url( get_option( 'woocommerce_woo_wallet_endpoint', 'woo-wallet' ), 'transfer', wc_get_page_permalink( 'myaccount' ) ) ) : add_query_arg( 'wallet_action', 'transfer' ),
			'icon'  => 'dashicons dashicons-randomize',
		),
		'transaction_details' => array(
			'title' => apply_filters( 'woo_wallet_account_transaction_menu_title', __( 'Transactions', 'woo-wallet' ) ),
			'url'   => $is_rendred_from_myaccount ? esc_url( wc_get_account_endpoint_url( get_option( 'woocommerce_woo_wallet_transactions_endpoint', 'woo-wallet-transactions' ) ) ) : add_query_arg( 'wallet_action', 'view_transactions' ),
			'icon'  => 'dashicons dashicons-list-view',
		),
	),
	$is_rendred_from_myaccount
);
?>

<div class="woo-wallet-my-wallet-container">
	<div class="woo-wallet-sidebar">
		<h3 class="woo-wallet-sidebar-heading"><a href="<?php echo $is_rendred_from_myaccount ? esc_url( wc_get_account_endpoint_url( get_option( 'woocommerce_woo_wallet_endpoint', 'woo-wallet' ) ) ) : esc_url( get_permalink() ); ?>"><?php echo apply_filters( 'woo_wallet_account_menu_title', __( 'My Wallet Gro', 'woo-wallet' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></h3>
		<ul>
			<?php foreach ( $menu_items as $item => $menu_item ) : ?>
				<?php if ( apply_filters( 'woo_wallet_is_enable_' . $item, true ) ) : ?>
					<li class="card"><a href="<?php echo esc_url( $menu_item['url'] ); ?>" ><span class="<?php echo esc_attr( $menu_item['icon'] ); ?>"></span><p><?php echo esc_html( $menu_item['title'] ); ?></p></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php do_action( 'woo_wallet_menu_items' ); ?>
		</ul>
	</div>
	<div class="woo-wallet-content">
		<div class="woo-wallet-content-heading">
			<h3 class="woo-wallet-content-h3"><?php esc_html_e( 'Balance', 'woo-wallet' ); ?></h3>
			<p class="woo-wallet-price"><?php echo woo_wallet()->wallet->get_wallet_balance( get_current_user_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		</div>
		<div style="clear: both"></div>
		<hr/>
		<?php if ( ( isset( $wp->query_vars['woo-wallet'] ) && ! empty( $wp->query_vars['woo-wallet'] ) ) || isset( $_GET['wallet_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<?php if ( apply_filters( 'woo_wallet_is_enable_top_up', true ) && ( ( isset( $wp->query_vars['woo-wallet'] ) && 'add' === $wp->query_vars['woo-wallet'] ) || ( isset( $_GET['wallet_action'] ) && 'add' === $_GET['wallet_action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended 
				?>
				<form method="post" action="" onsubmit="return validateForm()">
					<div class="woo-wallet-add-amount">
						<label for="woo_wallet_balance_to_add"><?php esc_html_e( 'Enter amount', 'woo-wallet' ); ?></label>
						<?php
						$min_amount = woo_wallet()->settings_api->get_option( 'min_topup_amount', '_wallet_settings_general', 0 );
						$max_amount = woo_wallet()->settings_api->get_option( 'max_topup_amount', '_wallet_settings_general', '' );
						?>
						
						<input type="number" step="0.01" min="<?php echo esc_attr( $min_amount ); ?>" max="<?php echo esc_attr( $max_amount ); ?>" name="woo_wallet_balance_to_add" id="woo_wallet_balance_to_add" class="woo-wallet-balance-to-add" required="" />
						<?php wp_nonce_field( 'woo_wallet_topup', 'woo_wallet_topup' ); ?>
						<input type="submit" onclick="submitForm()" name="woo_add_to_wallet" class="woo-add-to-wallet" value="<?php esc_html_e( 'Add', 'woo-wallet' ); ?>" />
						
						<style>
							.all-payment {
								margin-top: 50px;
								display: flex;
								flex-wrap: wrap;
								gap: 10px;
							}
							.gatewaya-box {
								display: flex;
								align-items: center;
								/* padding: 10px 20px;  */
								/* margin: 10px;  */
								border: 1px solid #ccc; 
								border-radius: 5px; 
								background: #fff;
								cursor: pointer;
								width: calc(33.33% - 20px); /* Adjust this value to change the number of columns */
								box-sizing: border-box;
							}
							.gatewaya-box img {
								width: 20%;
								height: auto;
								margin-right: 10px;
							}
							.gatewaya-box input[type="radio"] {
								display: none;
							}
							.gatewaya-box input[type="radio"] + label {
								background-image: none;
								padding: 25px 0 0 0;
							}
							.gatewaya-box label {
								flex-grow: 1;
								cursor: pointer;
								display: flex;
								align-items: center;
								text-align: center;
								height: 100px;
								padding: 38px 0;
								margin: 0;
							}
							.gatewaya-box:hover {
								border-color: #007cba;
								background: #f1f1f1;
							}
							.gatewaya-box input[type="radio"]:checked + label {
								border-color: #007cba;
								background: #245F9B;
								color: #fff;
								border-radius: 5px;
							}
						</style>
						
						<div class="all-payment">
							<?php 
							// Check if WooCommerce is active
							
							if (class_exists('Wc_Payment_Gateways')){
								// Get active payment gateways
								$payment_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
								// Loop through the active payment gateways and display their names
								foreach($payment_gateways as $gateway) {

									if($gateway->id == "wallet") continue;
									$image_url = $gateway->get_icon();
									$title = $gateway->get_title();
									$gateway_id = $gateway->id;
									$gateway_min_amount = get_option( 'wc_settings_min_' . $gateway_id, 0 );

									?>
									<div class="gatewaya-box">
										<input type="radio" required id="<?php echo $gateway_id;?>" name="selected_payment_gateway" value="<?php echo $gateway_id;?>">
										<label class="gatewaya-box-label" for="<?php echo $gateway_id;?>">
											<span><?php echo $title; ?></span>
											<br>
											<span><?php echo "Minimum Deposit: " . $gateway_min_amount;?></span>
										</label>
									</div>
									<?php
									
								}
							}
							?>
						</div>
					</div>
				</form>

			<?php } elseif ( apply_filters( 'woo_wallet_is_enable_transfer', 'on' === woo_wallet()->settings_api->get_option( 'is_enable_wallet_transfer', '_wallet_settings_general', 'on' ) ) && ( ( isset( $wp->query_vars['woo-wallet'] ) && 'transfer' === $wp->query_vars['woo-wallet'] ) || ( isset( $_GET['wallet_action'] ) && 'transfer' === $_GET['wallet_action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?> 
				<form method="post" action="" id="woo_wallet_transfer_form">
					<p class="woo-wallet-field-container form-row form-row-wide">
						<label for="woo_wallet_transfer_user_id"><?php esc_html_e( 'Select whom to transfer', 'woo-wallet' ); ?> <?php
						if ( apply_filters( 'woo_wallet_user_search_exact_match', true ) ) {
							esc_html_e( '(Email)', 'woo-wallet' );
						}
						?>
							</label>
						<select name="woo_wallet_transfer_user_id" class="woo-wallet-select2" required=""></select>
					</p>
					<p class="woo-wallet-field-container form-row form-row-wide">
						<label for="woo_wallet_transfer_amount"><?php esc_html_e( 'Amount', 'woo-wallet' ); ?></label>
						<input type="number" step="0.01" min="<?php echo woo_wallet()->settings_api->get_option( 'min_transfer_amount', '_wallet_settings_general', 0 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="woo_wallet_transfer_amount" required=""/>
					</p>
					<p class="woo-wallet-field-container form-row form-row-wide">
						<label for="woo_wallet_transfer_note"><?php esc_html_e( 'What\'s this for', 'woo-wallet' ); ?></label>
						<textarea name="woo_wallet_transfer_note"></textarea>
					</p>
					<p class="woo-wallet-field-container form-row">
						<?php wp_nonce_field( 'woo_wallet_transfer', 'woo_wallet_transfer' ); ?>
						<input type="submit" class="button" name="woo_wallet_transfer_fund" value="<?php esc_html_e( 'Proceed to transfer', 'woo-wallet' ); ?>" />
					</p>
				</form>
			<?php } ?> 
			<?php do_action( 'woo_wallet_menu_content' ); ?>
		<?php } elseif ( apply_filters( 'woo_wallet_is_enable_transaction_details', true ) ) { ?>
			<?php $transactions = get_wallet_transactions( array( 'limit' => apply_filters( 'woo_wallet_transactions_count', 10 ) ) ); ?>
			<?php if ( ! empty( $transactions ) ) { ?>
				<ul class="woo-wallet-transactions-items">
					<?php foreach ( $transactions as $transaction ) : ?> 
						<li>
							<div>
								<p><?php echo $transaction->details; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
								<small><?php echo wc_string_to_datetime( $transaction->date )->date_i18n( wc_date_format() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></small>
							</div>
							<div class="woo-wallet-transaction-type-<?php echo esc_attr( $transaction->type ); ?>">
								<?php
								echo 'credit' === $transaction->type ? '+' : '-';
								echo wc_price( apply_filters( 'woo_wallet_amount', $transaction->amount, $transaction->currency, $transaction->user_id ), woo_wallet_wc_price_args( $transaction->user_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php
			} else {
				esc_html_e( 'No transactions found', 'woo-wallet' );
			}
		}
		?>
	</div>
	
</div>
<?php
do_action( 'woo_wallet_after_my_wallet_content' );

?>
<script>
function submitForm() {
    var selectedGateway = document.querySelector('input[name="selected_payment_gateway"]:checked');
    if (!selectedGateway) {
        alert('Please select a payment method.');
        return;
    }
    
    var form = document.getElementById('wallet_form');
    form.submit();
}
function validateForm() {
        var selectedAmount = parseFloat(document.getElementById("woo_wallet_balance_to_add").value);
        var selectedGateway = document.querySelector('input[name="selected_payment_gateway"]:checked');
        if (!selectedGateway) {
            alert("Please select a payment gateway.");
            return false;
        }
        var minAmount = parseFloat(selectedGateway.nextElementSibling.querySelector('span:last-child').textContent.replace("Minimum Deposit: ", ""));
        if (selectedAmount < minAmount) {
            alert("Amount must be at least Diposit: " + minAmount);
            return false;
        }
        return true;
    }
</script>
