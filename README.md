# Wordpress Plugin Ajax , Http Api

WordPress Plugin ราคาทองวันนี้
Api URL : https://www.aagold-th.com/price/daily/

## Shortcode
`[gold-price-today]`

## Screen shot
![alt text](https://github.com/anuchit33/gold-price-today/blob/master/inc/images/screen-shot.png)


### 1.Header Requirements
```
<?php
/*
Plugin Name: Gold Price Today
Plugin URI: https://github.com/anuchit33/gold-price-today
Description: ราคาทองคำวันนี้
Author: Anuchit Yai-in
Version: 0.0.1
*/
```


### 2.สร้าง Plugin Class(OOP)
```
class GoldPriceToday {
    function __construct() {
    }
}
new GoldPriceToday();
```

### 3.สร้าง Shortcode และการ  Handle display
```
class GoldPriceToday {
    function __construct() {
      # Shortcode
      add_shortcode('gold-price-today', array($this, 'wp_shortcode_display'));
    }
}

```
- wp_shortcode_display
```
    function wp_shortcode_display(){

            ob_start();
            require_once( dirname(__FILE__) . '/templates/frontend/table-gold-price.php');
            $content = ob_get_contents();
            ob_end_clean();
    
            return $content;
        
    }
```

### 4.สร้าง template
สร้างไฟล์ `gold-price-today/themepates/frontend/table-gold-price.php`
```
<?php
# wp_enqueue_style
wp_enqueue_style('style-css', '/wp-content/plugins/gold-price-thai/inc/css/style.css');
?>
<h1>ราคาทองวันนี้</h1>
<input type="date" value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>" name="date" id="inputDate" />
<br/>
<div class="row">
    <div class="col">
        <table class="table table-goldprice ">
            <tbody>
                <tr>
                    <td class="bg" colspan="2">ทองคำ 96.5% (บาทละ)</td>
                </tr>
                <tr>
                    <td class="text-center"><small>ราคารับซื้อ</small></td>
                    <td class="text-center"><small>ราคาขายออก</small></td>
                </tr>
                <tr>
                    <td class="text-center"><span id="bar965_sell_baht"></span></td>
                    <td class="text-center"><span id="bar965_buy_baht"></span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col">
            <table class="table table-goldprice ">
                <tbody>
                    <tr>
                        <td class="bg" colspan="2">ทองรูปพรรณ 96.5% (บาทละ)</td>
                    </tr>
                    <tr>
                        <td class="text-center"><small>ราคารับซื้อ</small></td>
                        <td class="text-center"><small>ราคาขายออก</small></td>
                    </tr>
                    <tr>
                        <td class="text-center"><span id="ornament965_sell_baht"></span></td>
                        <td class="text-center"><span id="ornament965_buy_baht"></span></td>
                    </tr>
                </tbody>
            </table>
    </div>
</div>

<script type="text/javascript">
    var $ajax_nonce = '<?=wp_create_nonce( "ajax_security" )?>';
    var $ajax_url = '<?=admin_url('admin-ajax.php')?>';

    jQuery(document).ready(function ($) {
        var getGoldPriceByDate = function (date = '') {
            var data = {
                action: 'get_gold_price',
                security: $ajax_nonce,
                date: date
            };

            $.ajax({
                type: 'get',
                url: $ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {
                    console.log('response', response)

                    $('#bar965_sell_baht').html(response.bar965_sell_baht)
                    $('#bar965_buy_baht').html(response.bar965_buy_baht)
                    $('#ornament965_sell_baht').html(response.ornament965_sell_baht)
                    $('#ornament965_buy_baht').html(response.ornament965_buy_baht)
                }
            });
        }

        // ready load
        getGoldPriceByDate($('#inputDate').val())

        // event
        $('#inputDate').change(function(){
            getGoldPriceByDate($('#inputDate').val())
        })

    });
</script>
```

### 5. add_action สำหรับ handle ajax request
- doc wp_ajax https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)
- doc wp_ajax_nopriv https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_nopriv_(action)
- doc http api https://developer.wordpress.org/plugins/http-api/
```
    function __construct() {
        ...
        # add action get
        add_action('wp_ajax_get_gold_price', array($this, 'wp_api_get_gold_price'));
        add_action('wp_ajax_nopriv_get_gold_price', array($this, 'wp_api_get_gold_price'));
    }
```
- wp_api_get_gold_price
```
    function wp_api_get_gold_price(){

        # check ajax_security
        check_ajax_referer('ajax_security', 'security');

        # filter date
        $date = isset($_GET['date'])?$_GET['date']:date('Y-m-d');

        # fetch gold price
        $args = array();
        $url = 'https://www.aagold-th.com/price/daily/?date='.$date;
        $response = wp_remote_get( $url );
        $body = wp_remote_retrieve_body( $response );
        wp_send_json(json_decode($body,true)[0],200);
        die();
    }
```

