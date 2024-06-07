<?php
/**
 * Mollie client.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

use Pronamic\WordPress\DateTime\DateTime;
use Pronamic\WordPress\Http\Facades\Http;

/**
 * Client class
 */
class Client {
	/**
	 * Mollie API endpoint URL
	 *
	 * @var string
	 */
	const API_URL = 'https://api.mollie.com/v2/';

	/**
	 * Mollie API Key ID
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Constructs and initializes an Mollie client object
	 *
	 * @param string $api_key Mollie API key.
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * Get version.
	 *
	 * @return string
	 */
	private function get_version() {
		$package_file = __DIR__ . '/../package.json';

		$package_json = \file_get_contents( $package_file, true );

		if ( false === $package_json ) {
			return '';
		}

		$package_data = \json_decode( $package_json );

		if ( ! is_object( $package_data ) ) {
			return '';
		}

		if ( ! property_exists( $package_data, 'version' ) ) {
			return '';
		}

		return $package_data->version;
	}

	/**
	 * Get user agent value for requests to Mollie.
	 *
	 * @link https://github.com/pronamic/wp-pronamic-pay-mollie/issues/13
	 * @return string
	 */
	public function get_user_agent() {
		return implode(
			' ',
			[
				/**
				 * Pronamic Mollie version.
				 *
				 * @link https://github.com/pronamic/pronamic-pay/issues/12
				 */
				'PronamicMollie/' . $this->get_version(),
				/**
				 * Pronamic - Mollie user agent token.
				 *
				 * @link https://github.com/pronamic/pronamic-pay/issues/12
				 */
				'uap/FyuVeDDqnKdzdry7',
				/**
				 * WordPress version.
				 *
				 * @link https://github.com/WordPress/WordPress/blob/f9db66d504fc72942515f6c0ed2b63aee7cef876/wp-includes/class-wp-http.php#L183-L192
				 */
				'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
			]
		);
	}

	/**
	 * Helper function to check if the HTTP timeout could be increased.
	 * 
	 * @link https://github.com/pronamic/wp-pay-core/issues/170
	 * @return bool
	 */
	private function should_increase_http_timeout() {
		return (
			\wp_doing_cron()
				||
			\defined( 'WP_CLI' ) && WP_CLI
				||
			\defined( 'PRONAMIC_ACTION_SCHEDULER_CONTEXT' )
		);
	}

	/**
	 * Send request with the specified action and parameters
	 *
	 * @param string $url    URL.
	 * @param string $method HTTP method to use.
	 * @param mixed  $data   Request data.
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 * @throws \Exception Throws exception when error occurs.
	 */
	public function send_request( $url, $method = 'GET', $data = null ) {
		// Request.
		$args = [
			'method'     => $method,
			'user-agent' => $this->get_user_agent(),
			'headers'    => [
				'Authorization' => 'Bearer ' . $this->api_key,
			],
			'timeout'    => $this->should_increase_http_timeout() ? 30 : 5,
		];

		if ( null !== $data ) {
			$args['headers']['Content-Type'] = 'application/json';

			$args['body'] = \wp_json_encode( $data );
		}

		$response = Http::request( $url, $args );

		$data = $response->json();

		// Object.
		if ( ! \is_object( $data ) ) {
			$code = $response->status();

			throw new \Exception(
				\sprintf(
					'Could not JSON decode Mollie response to an object (HTTP Status Code: %s).',
					\esc_html( (string) $code )
				),
				(int) $code
			);
		}

		// Mollie error from JSON response.
		if ( isset( $data->status, $data->title, $data->detail ) ) {
			throw new Error(
				(int) $data->status,
				\esc_html( $data->title ),
				\esc_html( $data->detail )
			);
		}

		return $data;
	}

	/**
	 * Post data to URL.
	 *
	 * @param string $url  URL.
	 * @param mixed  $data Data.
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 */
	private function post( string $url, $data = null ) {
		return $this->send_request( $url, 'POST', $data );
	}

	/**
	 * Get data from URL.
	 *
	 * @param string $url URL.
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 */
	private function get( string $url ) {
		return $this->send_request( $url, 'GET' );
	}

	/**
	 * Get URL.
	 *
	 * @param string   $endpoint   URL endpoint.
	 * @param string[] $parts      Parts.
	 * @param string[] $parameters Parameters.
	 * @return string
	 */
	private function get_url( $endpoint, array $parts = [], array $parameters = [] ) {
		$url = self::API_URL . \strtr( $endpoint, $parts );

		if ( \count( $parameters ) > 0 ) {
			$url .= '?' . \http_build_query( $parameters, '', '&' );
		}

		return $url;
	}

	/**
	 * Get profile.
	 *
	 * @param string $profile_id Mollie profile ID.
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 */
	public function get_profile( $profile_id ) {
		return $this->get(
			$this->get_url(
				'profiles/*id*',
				[
					'*id*' => $profile_id,
				]
			)
		);
	}

	/**
	 * Get current profile.
	 *
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 */
	public function get_current_profile() {
		return $this->get_profile( 'me' );
	}

	/**
	 * Create order.
	 *
	 * @param OrderRequest $request Order request.
	 * @return Order
	 */
	public function create_order( OrderRequest $request ) {
		$object = $this->post(
			$this->get_url(
				'orders',
				[],
				[
					'embed' => 'payments',
				]
			),
			$request
		);

		$order = Order::from_json( $object );

		return $order;
	}

	/**
	 * Create payment.
	 *
	 * @param PaymentRequest $request Payment request.
	 * @return Payment
	 */
	public function create_payment( PaymentRequest $request ) {
		$object = $this->post(
			$this->get_url( 'payments' ),
			$request
		);

		$payment = Payment::from_json( $object );

		return $payment;
	}

	/**
	 * Create shipment for an order.
	 *
	 * @param string $order_id Order ID.
	 * @return Shipment
	 */
	public function create_shipment( $order_id ) {
		$response = $this->post(
			$this->get_url(
				'orders/*orderId*/shipments',
				[
					'*orderId*' => $order_id,
				]
			)
		);

		$shipment = Shipment::from_json( $response );

		return $shipment;
	}

	/**
	 * Get order.
	 *
	 * @param string $order_id Order ID.
	 * @return Order
	 */
	public function get_order( string $order_id ): Order {
		$response = $this->get(
			$this->get_url(
				'orders/*id*',
				[
					'*id*' => $order_id,
				],
				[
					'embed' => 'payments',
				]
			)
		);

		$order = Order::from_json( $response );

		return $order;
	}

	/**
	 * Get payments.
	 *
	 * @return bool|object
	 */
	public function get_payments() {
		return $this->get( $this->get_url( 'payments' ) );
	}

	/**
	 * Get refunds.
	 *
	 * @return bool|object
	 */
	public function get_refunds() {
		return $this->get( $this->get_url( 'refunds' ) );
	}

	/**
	 * Get chargebacks.
	 *
	 * @return bool|object
	 */
	public function get_chargebacks() {
		return $this->get( $this->get_url( 'chargebacks' ) );
	}

	/**
	 * Get settlements.
	 *
	 * @return bool|object
	 */
	public function get_settlements() {
		return $this->get( $this->get_url( 'settlements' ) );
	}

	/**
	 * Get invoices.
	 *
	 * @return bool|object
	 */
	public function get_invoices() {
		return $this->get( $this->get_url( 'invoices' ) );
	}

	/**
	 * Get payment.
	 *
	 * @param string               $payment_id Mollie payment ID.
	 * @param array<string, mixed> $parameters Parameters.
	 * @return Payment
	 * @throws \InvalidArgumentException Throws exception on empty payment ID argument.
	 */
	public function get_payment( $payment_id, $parameters = [] ) {
		if ( empty( $payment_id ) ) {
			throw new \InvalidArgumentException( 'Mollie payment ID can not be empty string.' );
		}

		$object = $this->get(
			$this->get_url(
				'payments/*id*',
				[
					'*id*' => $payment_id,
				],
				$parameters
			)
		);

		$payment = Payment::from_json( $object );

		return $payment;
	}

	/**
	 * Get issuers
	 *
	 * @return array<string>
	 */
	public function get_issuers() {
		$response = $this->get(
			$this->get_url(
				'methods/ideal',
				[],
				[
					'include' => 'issuers',
				]
			)
		);

		$issuers = [];

		if ( isset( $response->issuers ) ) {
			foreach ( $response->issuers as $issuer ) {
				$id   = $issuer->id;
				$name = $issuer->name;

				if ( null === $id || null === $name ) {
					continue;
				}

				$issuers[ $id ] = $name;
			}
		}

		return $issuers;
	}

	/**
	 * Get all payment methods.
	 *
	 * @link https://docs.mollie.com/reference/v2/methods-api/list-all-methods
	 * @return object
	 */
	public function get_all_payment_methods() {
		$response = $this->get( $this->get_url( 'methods/all' ) );

		return $response;
	}

	/**
	 * Create customer.
	 *
	 * @param Customer $customer Customer.
	 * @return Customer
	 * @throws Error Throws Error when Mollie error occurs.
	 * @since 1.1.6
	 */
	public function create_customer( Customer $customer ) {
		$response = $this->post(
			$this->get_url( 'customers' ),
			$customer
		);

		if ( \property_exists( $response, 'id' ) ) {
			$customer->set_id( $response->id );
		}

		return $customer;
	}

	/**
	 * Get customer.
	 *
	 * @param string $customer_id Mollie customer ID.
	 *
	 * @return null|object
	 * @throws \InvalidArgumentException Throws exception on empty customer ID argument.
	 * @throws Error Throws Error when Mollie error occurs.
	 */
	public function get_customer( $customer_id ) {
		if ( empty( $customer_id ) ) {
			throw new \InvalidArgumentException( 'Mollie customer ID can not be empty string.' );
		}

		try {
			return $this->get(
				$this->get_url(
					'customers/*id*',
					[
						'*id*' => $customer_id,
					]
				)
			);
		} catch ( Error $error ) {
			if ( 404 === $error->get_status() ) {
				return null;
			}

			throw $error;
		}
	}

	/**
	 * Create mandate.
	 *
	 * @param string                $customer_id Customer ID.
	 * @param array<string, string> $parameters  Parameters.
	 * @return Mandate
	 * @throws \Exception Throws exception when mandate creation failed.
	 */
	public function create_mandate( $customer_id, array $parameters = [] ) {
		$response = $this->post(
			$this->get_url(
				'customers/*customerId*/mandates',
				[
					'*customerId*' => $customer_id,
				]
			),
			$parameters
		);

		return Mandate::from_json( $response );
	}

	/**
	 * Get mandate.
	 *
	 * @param string $mandate_id Mollie mandate ID.
	 * @param string $customer_id Mollie customer ID.
	 * @return object
	 * @throws \InvalidArgumentException Throws exception on empty mandate ID argument.
	 */
	public function get_mandate( $mandate_id, $customer_id ) {
		if ( '' === $mandate_id ) {
			throw new \InvalidArgumentException( 'Mollie mandate ID can not be empty string.' );
		}

		if ( '' === $customer_id ) {
			throw new \InvalidArgumentException( 'Mollie customer ID can not be empty string.' );
		}

		return $this->get(
			$this->get_url(
				'customers/*customerId*/mandates/*id*',
				[
					'*customerId*' => $customer_id,
					'*id*'         => $mandate_id,
				]
			)
		);
	}

	/**
	 * Get mandates for customer.
	 *
	 * @param string $customer_id Mollie customer ID.
	 * @return object
	 * @throws \InvalidArgumentException Throws exception on empty customer ID argument.
	 */
	public function get_mandates( $customer_id ) {
		if ( '' === $customer_id ) {
			throw new \InvalidArgumentException( 'Mollie customer ID can not be empty string.' );
		}

		return $this->get(
			$this->get_url(
				'customers/*customerId*/mandates',
				[
					'*customerId*' => $customer_id,
				],
				[
					'limit' => '250',
				]
			)
		);
	}

	/**
	 * Create payment refund.
	 *
	 * @param string        $payment_id     Mollie payment ID.
	 * @param RefundRequest $refund_request Refund request.
	 * @return Refund
	 */
	public function create_refund( $payment_id, RefundRequest $refund_request ) {
		$response = $this->post(
			$this->get_url(
				'payments/*id*/refunds',
				[
					'*id*' => $payment_id,
				]
			),
			$refund_request
		);

		return Refund::from_json( $response );
	}

	/**
	 * Create order refund.
	 *
	 * @param string             $order_id       Mollie order ID.
	 * @param OrderRefundRequest $refund_request Order refund request.
	 * @return Refund
	 */
	public function create_order_refund( string $order_id, OrderRefundRequest $refund_request ): Refund {
		$response = $this->post(
			$this->get_url(
				'orders/*orderId*/refunds',
				[
					'*orderId*' => $order_id,
				]
			),
			$refund_request
		);

		return Refund::from_json( $response );
	}

	/**
	 * Get payment chargebacks.
	 *
	 * @param string               $payment_id Mollie payment ID.
	 * @param array<string, mixed> $parameters Parameters.
	 * @return array<Chargeback>
	 */
	public function get_payment_chargebacks( $payment_id, $parameters ) {
		$object = $this->get(
			$this->get_url(
				'payments/*paymentId*/chargebacks',
				[
					'*paymentId*' => $payment_id,
				],
				$parameters
			)
		);

		$chargebacks = [];

		if ( \property_exists( $object, '_embedded' ) && \property_exists( $object->_embedded, 'chargebacks' ) ) {
			foreach ( $object->_embedded->chargebacks as $chargeback_object ) {
				$chargebacks[] = Chargeback::from_json( $chargeback_object );
			}
		}

		return $chargebacks;
	}

	/**
	 * Get payment refunds.
	 *
	 * @param string               $payment_id Mollie payment ID.
	 * @param array<string, mixed> $parameters Parameters.
	 * @return array<Refund>
	 */
	public function get_payment_refunds( $payment_id, $parameters ) {
		$object = $this->get(
			$this->get_url(
				'payments/*paymentId*/refunds',
				[
					'*paymentId*' => $payment_id,
				],
				$parameters
			)
		);

		$refunds = [];

		if ( \property_exists( $object, '_embedded' ) && \property_exists( $object->_embedded, 'refunds' ) ) {
			foreach ( $object->_embedded->refunds as $refund_object ) {
				$refunds[] = Refund::from_json( $refund_object );
			}
		}

		return $refunds;
	}

	/**
	 * Get organization.
	 *
	 * @param string $organization_id Mollie organization ID.
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 * @link https://docs.mollie.com/reference/v2/organizations-api/get-organization
	 */
	public function get_organization( $organization_id ) {
		return $this->get(
			$this->get_url(
				'organizations/*id*',
				[
					'*id*' => $organization_id,
				]
			)
		);
	}

	/**
	 * Get balances.
	 *
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 * @link https://docs.mollie.com/reference/v2/balances-api/list-balances
	 */
	public function get_balances() {
		return $this->get( $this->get_url( 'balances' ) );
	}

	/**
	 * Get balance.
	 *
	 * @param string $balance_id Mollie balance ID.
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 * @link https://docs.mollie.com/reference/v2/balances-api/get-balance
	 */
	public function get_balance( $balance_id ) {
		return $this->get(
			$this->get_url(
				'balances/*balanceId*',
				[
					'*balanceId*' => $balance_id,
				]
			)
		);
	}

	/**
	 * Get balance transactions.
	 *
	 * @param string                $balance_id  Mollie balance ID.
	 * @param array<string, string> $parameters  Parameters.
	 * @return object
	 * @throws Error Throws Error when Mollie error occurs.
	 * @link https://docs.mollie.com/reference/v2/balances-api/list-balance-transactions
	 */
	public function get_balance_transactions( $balance_id, array $parameters = [] ) {
		return $this->get(
			$this->get_url(
				'balances/*balanceId*/transactions',
				[
					'*balanceId*' => $balance_id,
				],
				$parameters
			)
		);
	}
}
