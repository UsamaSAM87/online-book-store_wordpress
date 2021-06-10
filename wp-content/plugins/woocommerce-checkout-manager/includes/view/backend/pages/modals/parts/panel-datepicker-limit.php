<# if ( data.date_limit == 'variable' ) { #>
<div class="options_group wooccm-premium">
  <p class="form-field">
    <label><?php esc_html_e('Days before', 'woocommerce-checkout-manager'); ?></label>
    <input class="short" type="number" placeholder="3" min="0" max="365" name="date_limit_variable_min" value="{{data.date_limit_variable_min}}">
    <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
  </p>
  <p class="form-field">
    <label><?php esc_html_e('Days after', 'woocommerce-checkout-manager'); ?></label>
    <input class="short" type="number" placeholder="3" min="0" max="365" name="date_limit_variable_max" value="{{data.date_limit_variable_max}}">
    <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
  </p>
</div>
<# } else if ( data.date_limit == 'fixed' ) { #>
<div class="options_group wooccm-premium wooccm-enhanced-between-dates">
  <p class="form-field dimensions_field">
    <label for="product_length"><?php esc_html_e('Between dates', 'woocommerce-checkout-manager'); ?></label>
    <span class="wrap">
      <input style="width:48.1%" type="text" class="short " name="date_limit_fixed_min" value="{{data.date_limit_fixed_min}}" placeholder="<?php esc_html_e('From… YYYY-MM-DD', 'woocommerce-checkout-manager'); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
      <input style="width:48.1%;margin: 0;" type="text" class="short" name="date_limit_fixed_max" value="{{data.date_limit_fixed_max}}" placeholder="<?php esc_html_e('From… YYYY-MM-DD', 'woocommerce-checkout-manager'); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
    </span>
    <span class="description premium">(<?php esc_html_e('This is a premium feature', 'woocommerce-checkout-manager'); ?>)</span>
  </p>
</div>
<# } #>