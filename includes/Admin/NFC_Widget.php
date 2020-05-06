<?php
namespace KAMAL\NFC\Admin;

class NFC_Widget extends \WP_Widget {
    public function __construct() {
        $widget_options = [
            'classname'   => 'nfc_widget',
            'description' => __( 'NFC Widget to display post category with follow button', 'nfc' ),
        ];
        parent::__construct( 'nfc_widget', __( 'NFC Category', 'nfc' ), $widget_options );

        add_action( 'wp_ajax_nfc_ajax_get_id', [$this, 'nfc_ajax_get_id'] );
        add_action( 'wp_ajax_nopriv_nfc_ajax_get_id', [$this, 'nfc_ajax_get_id_no'] );
        add_action( 'pre_get_posts', [$this, 'set_posts_cat'] );
    }

    /**
     * Set Posts Query
     *
     * @param $query
     */
    public function set_posts_cat( $q ) {
        if ( is_user_logged_in() ) { // First check if we have a logged in user before doing anything
            if ( $q->is_home() // Only targets the main page, home page
                 && $q->is_main_query() // Only targets the main query
            ) {
                if ( is_active_widget( false, false, 'nfc_widget', true ) ) {
                    // Get the current logged in user obejct
                    $current_logged_in_user = wp_get_current_user();

                    $term = get_user_meta( $current_logged_in_user->ID, 'post_tag_ids', true );

                    if ( $term ) {
                        $q->set( 'cat', $term );
                    }
                }
            }
        }

    }

    /**
     * Save, Remove category ID in user meta.
     *
     * @return mixed
     */
    public function nfc_ajax_get_id() {
        global $wpdb;

        if ( !isset( $_POST ) || empty( $_POST ) || !is_user_logged_in() ) {
            echo __( 'Oops! Try again.', 'nfc' );
            exit;
        }
        $current_user = wp_get_current_user();
        $saved_ids    = get_user_meta( $current_user->ID, 'post_tag_ids', true );

        $id_to_add = isset( $_POST["data"] ) ? $_POST["data"] : '';

        if ( in_array( $id_to_add, $saved_ids ) ) {

            if (  ( $key = array_search( $id_to_add, $saved_ids ) ) !== false ) {
                unset( $saved_ids[$key] );
                update_user_meta( $current_user->ID, 'post_tag_ids', $saved_ids );
                echo wp_send_json( ['value' => sanitize_key( 'unfollowed' )] );
            }

        } else {
            //if list is empty, initialize it as an empty array
            if ( $saved_ids == '' ) {
                $saved_ids = array();
            }

            // push the new ID inside the array
            array_push( $saved_ids, $id_to_add );

            if ( update_user_meta( $current_user->ID, 'post_tag_ids', $saved_ids ) ) {
                //echo $id_to_add;
                echo wp_send_json( ['value' => sanitize_key( 'followed' )] );
            } else {
                echo __( 'Failed: Could not update user meta.', 'nfc' );
            }
        }

        die();

    }

    /**
     * No logged in user message
     *
     * @return string
     */
    public function nfc_ajax_get_id_no() {
        echo __( "you're not logged in, please login first.", 'nfc' );
    }

    /**
     * Register NFC Category Widget
     *
     * @return void
     */
    public function nfc_widget_reg() {
        register_widget( $this );
    }
    /**
     * Display Widget
     *
     * @param $args
     * @param $instance
     */
    public function widget( $args, $instance ) {

        echo $args['before_widget'];
        if ( !empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $current_logged_in_user = wp_get_current_user();
        $user_followed_ids      = get_user_meta( $current_logged_in_user->ID, 'post_tag_ids', true );

        $categories = get_categories( array(
            'taxonomy'   => 'category',
            'orderby'    => 'name',
            'parent'     => 0,
            'hide_empty' => 0,
            'exclude'    => $user_followed_ids,
        ) );

        $follow_cat_lists = get_categories( array(
            'taxonomy'   => 'category',
            'orderby'    => 'name',
            'parent'     => 0,
            'hide_empty' => 0,
            'include'    => $user_followed_ids,
        ) );

        echo "<ul class='nfc-category-list'>";
        echo "<div class='nfc-my-category-list'>";
        if ( is_user_logged_in() ):

            if ( !empty( $user_followed_ids ) ):
                echo "<h2>" . esc_html( 'My Category', 'nfc' ) . "</h2>";
                foreach ( $follow_cat_lists as $follow_cat_list ):
                    printf( '<li>%s <a data-cat-id="%s" href="javascript:void(0)" class="follow-cat">%s</a></li>', esc_html( $follow_cat_list->name ), esc_attr( $follow_cat_list->term_id ), __( 'Unfollow', 'nfc' ) );

                endforeach;
            endif;
        endif;
        echo "</div>";
        echo "<div class='nfc-all-category-list'>";
        echo "<h2>" . esc_html( 'Popular Category', 'nfc' ) . "</h2>";
        if ( !empty( $categories ) ):
            foreach ( $categories as $category ):
                printf( '<li>%s <a data-cat-id="%s" href="javascript:void(0)" class="follow-cat">%s</a></li>', esc_html( $category->name ), esc_attr( $category->term_id ), __( 'Follow', 'nfc' ) );
            endforeach;
        endif;
        echo "</div>";
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
        $title = !empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Follow Category', 'nfc' );
        ?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'nfc' );?></label>
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