<?php
namespace KAMAL\NFC\Admin;

class NFC_Widget extends \WP_Widget {
    public function __construct() {
        $widget_options = [
            'classname'   => 'nfc_wiget',
            'description' => __( 'NFC Wiget to display post category with follow button', 'nfc' ),
        ];
        parent::__construct( 'nfc_wiget', __( 'NFC Category', 'nfc' ), $widget_options );

        add_action( 'wp_ajax_nfc_ajax_get_id', [$this, 'nfc_ajax_get_id'] );
        add_action( 'wp_ajax_nopriv_nfc_ajax_get_id', [$this, 'nfc_ajax_get_id'] );
    }

    /**
     * @return mixed
     */
    public function nfc_ajax_get_id() {
        $data = isset( $_POST["data"] );
        return $data;
        die();
    }

    function nfc_widget_reg() {
        register_widget( $this );
    }
    /**
     * @param $args
     * @param $instance
     */
    public function widget( $args, $instance ) {

        echo $args['before_widget'];
        if ( !empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo "id=" . $this->nfc_ajax_get_id();
        $categories = get_categories( array(
            'taxonomy'   => 'category',
            'orderby'    => 'name',
            'parent'     => 0,
            'hide_empty' => 0,
        ) );
        echo "<ul class='nfc-category-list'>";

        foreach ( $categories as $category ):
            printf( '<li data-cat-id="%s">%s <a href="#" class="follow-cat">%s</a></li>', esc_attr( $category->term_id ), esc_html( $category->name ), __( 'Follow', 'nfc' ) );
        endforeach;
        echo "</ul>";
        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param  mixed  $instance
     * @return void
     */
    public function form( $instance ) {
        $title = !empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Follow Category', 'text_domain' );
        ?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' );?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
}

    /**
     * update widget form instance
     *
     * @param  mixed  $new_instance
     * @param  mixed  $old_instance
     * @return void
     */
    public function update( $new_instance, $old_instance ) {
        $instance          = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

        return $instance;
    }
}