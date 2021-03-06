# magento-coupon-exclude-products-dynamically-by-sku-depending-on-special-price-discount-percentage

The Magento module is written with the goal of applying a discount to all products of cart. But there are one condition if one product have already other discount in special price then this module will reset the price in cart and then apply the coupon. Also, this module will exclude products which have more discounts in special price than the discount will be applied by coupon.

##### Features: 
 - Apply cart coupon code to products if the products special price discounts are less than coupon discount.
 - Apply coupon on MRP if product is applicable for discount
 - Exclude not applicable products from applying discount. These products will be added with special prices.
 - Reset cart products price after cancel coupon.
 
##### Magento version: 1.5.0.0 to 1.9.2.4

##### Instructions:
  - Create one coupon code from Backend **Promotions > Shopping Cart Price Rules >add new rule**
  - Fill the forms
  - Leave **Conditions** blank
  - In **Actions** set **Discount Amount** as percentage
  - Also set rule "sku is not one of __(set any one sku - it will dynamically change depending on cart products)" in **Apply the rule only to cart items matching the following conditions** inside **Action** [For reference check screen shot]
  ![Coupon code rule](https://github.com/shahed-jamal/magento-coupon-exclude-products-dynamically-by-sku-depending-on-special-price-discount-percentage/blob/master/Screenshot/coupon%20actions.png "Coupon code rule action")
  - Now set system configuration in magento **system > configuration > Shahed Module > Coupon Code Custom Config**. Set "Coupon Code" and "Discount Percentage" created in privious steps. [For reference check screen shot]
![Coupon code system config](https://github.com/shahed-jamal/magento-coupon-exclude-products-dynamically-by-sku-depending-on-special-price-discount-percentage/blob/master/Screenshot/coupon_system_config.png "Coupon code system config")
