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
        add_action( 'wp_ajax_nopriv_nfc_ajax_get_id', [$this, 'nfc_ajax_get_id_no'] );
    }

    /**
     * @return mixed
     */
    public function nfc_ajax_get_id() {
        if ( is_user_logged_in() ) {
            $data         = isset( $_POST["data"] ) ? $_POST["data"] : '';
            $cat_ids      = serialize( $data );
            $current_user = wp_get_current_user();
            update_user_meta( $current_user->ID, 'post_cat_ids', $cat_ids );

            echo $data;
            die();
        }
    }

    public function nfc_ajax_get_id_no() {
        echo "you're not logged in, please login first.";
    }

    function nfc_widget_reg() {
        register_widget( $this );
    }
    /**
     * @param $args
     * @param $instance
     */
    public function widget( $args, $instance ) {
        //print_r();
        echo $args['before_widget'];
        if ( !empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $categories = get_categories( array(
            'taxonomy'   => 'category',
            'orderby'    => 'name',
            'parent'     => 0,
            'hide_empty' => 0,
        ) );
        echo "<div>";
        print_r( get_user_meta( wp_get_current_user()->ID, 'post_cat_ids', true ) );
        echo "</div>";
        echo "<ul class='nfc-category-list'>";
        //$this->nfc_ajax_get_id();
        foreach ( $categories as $category ):
            printf( '<li>%s <a data-cat-id="%s" href="#" class="follow-cat">%s</a></li>', esc_html( $category->name ), esc_attr( $category->term_id ), __( 'Follow', 'nfc' ) );
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