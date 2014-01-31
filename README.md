### Prediction.IO Magento Introduction

This extensions attempts to replace the Magento product recommendations ( related, upsell, crossells ) with a new PredictionIO based recommendation engine. 

## Functional Requirements 

- Ability to enable/disable the prediction.io functionality.
- Ability to switch between Prediction.io algorithms between. 
- Ability to replace Related products recommendations. 
- Accessible classes for extending 
- Clean implementation of the prediction.io SDK
- Stored History for not logged in Customers 
- Cron to Update and merge the history 

#### Prediction.io

PredictionIO is an open source machine learning server for software developers to create predictive features, such as personalization, recommendation and content discovery.

##  Technical Manifest

- Prediction.io Adapter Class
- Observer
- Model and Resource Collection for the NOT LOGGED IN Events 
- Cron to Process 

## Event Hooks

- Track Customers Using Cookie
- Product Page Load (View Action)
- Place Order (Convert Action)
- Add to Cart (Like Action)
- Merge History on Login 


## Event Data Model 

{
	id: 1,
	cookie_id: 6sis994jen383s,
	customer_id: 8484848484
	event_type: [view,like,convert,dislike],
	pio_iid: product_sku,
	pio_price: product_price,
	pio_itypes: "t2,t3,t3,t1"

}