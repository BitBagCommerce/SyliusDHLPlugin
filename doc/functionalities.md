# Functionalities

---
### Introduction

---
The plugin allows you to make a DHL (DE) courier order with one click of the mouse, without having to fill in the data each time.
This plugin supports communication with the DHL DE API, including exporting shipping data
and creating ready-made labels to print directly from the order.

### Usage

---
#### Adding Shipping Gate

After installation, users can add the shipping method corresponding to the service provider
to which they want to export shipments.

<div align="center">
    <img src="./images/shipping_method.png"/>
</div>
<br>

It then creates a new "shipping gateway" for the added shipping method.
When a customer selects this shipping method when ordering products, the order will be captured by the gateway.
<div align="center">
    <img src="./images/shipping_gateway.png"/>
</div>
<br>

Selects the shipping method and completes the form fields accordingly.

<div align="center">
    <img src="./images/shipping_gateway_details.png"/>
</div>
<br>

#### Shipping Exports
Once the shipping method and shipping gateway for the shipping provider are created,
customer can use this shipping method during a checkout. When the order is placed,
user can now go to the 'Export shipping data' section from Sylius Admin Panel and export chosen shipments.

After exporting the shipment, it is possible to download the label for printing.
<div align="center">
    <img src="./images/shipping_export.png"/>
</div>

The delivery letter and tracking code are also available from order details view in Sylius Admin Panel
<div align="center">
    <img src="./images/order_shipment.png"/>
</div>