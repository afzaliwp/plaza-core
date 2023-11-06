<?php

namespace AfzaliWP\PlazaDigital\Includes\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Slides extends Widget_Base {

	public function get_name() {
		return 'plaza-slides';
	}

	public function get_title() {
		return __( 'Plaza Slides', 'elementor' );
	}

	public function get_icon() {
		return 'fa fa-code';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'elementor' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'elementor' ),
				'type'  => Controls_Manager::URL,
			]
		);

		$repeater->add_control(
			'slide_background_color',
			[
				'label'     => __( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.swiper-slide' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'slides',
			[
				'label'  => __( 'Slides', 'elementor' ),
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);

		$this->add_control(
			'items_per_slide',
			[
				'label'   => __( 'Items Per Slide', 'elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
			]
		);

		$this->add_control(
			'delay',
			[
				'label'   => __( 'Delay', 'elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label'        => __( 'Show Arrows', 'elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor' ),
				'label_off'    => __( 'Hide', 'elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label'        => __( 'Show Pagination', 'elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor' ),
				'label_off'    => __( 'Hide', 'elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_responsive_control(
			'slide_height',
			[
				'label'      => __( 'Slide Height', 'elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-slide' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
        <div class="swiper"
             data-items-per-slide="<?php echo esc_attr( $settings[ 'items_per_slide' ] ); ?>"
             data-delay="<?php echo esc_attr( $settings[ 'delay' ] ); ?>"
             data-show-arrows="<?php echo esc_attr( $settings[ 'show_arrows' ] ); ?>"
             data-show-pagination="<?php echo esc_attr( $settings[ 'show_pagination' ] ); ?>">
            <div class="swiper-wrapper">
				<?php foreach ( $settings[ 'slides' ] as $slide ) : ?>
                    <a class="swiper-slide"
                       href="<?php echo $slide[ 'link' ][ 'url' ]; ?>"
                       style="background-image: url('<?php echo $slide[ 'image' ][ 'url' ]; ?>')"
                    >
                    </a>
				<?php endforeach; ?>
            </div>
			<?php if ( $settings[ 'show_pagination' ] ): ?>
                <div class="swiper-pagination"></div>
			<?php endif; ?>
			<?php if ( $settings[ 'show_arrows' ] ): ?>
                <div class="swiper-button-next">
                    <i class="plz-icon chevron-right-white"></i>
                </div>
                <div class="swiper-button-prev">
                    <i class="plz-icon chevron-left-white"></i>
                </div>
			<?php endif; ?>


        </div>

		<?php
	}
}
