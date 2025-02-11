# Mopinion Feedback Survey
Easy add feedback buttons and feedback forms to your website with the Mopinion.com Magento extension. Easy install, fast user insights. Mopinion is a leading user feedback collection and analysis tool.

## Description

Let's say that you want to capture user feedback on your Magento website. This Mopinion Feedback Survey extension makes that easy and is completely free.

Simply go to Stores &rarr; Mopinion Feedback Survey in your website admin dashboard, and activate the "Mopinion Feedback Survey" on your website.

A feedback button will be visible on your website. With the feedback form you can capture suggestions, bugs and compliments, and collect visual feedback (screenshots) about individual page elements. All feedback collected will be available on your personal Mopinion account, at https://app.mopinion.com, or it can be emailed straight in to your email inbox. The feedback survey is completely customisable, so easily add or change feedback questions, add your own design and logo, setup your own language and use pro-active and exit-intent triggers to decide where you want to show or hide your feedback form.

If you want to add the feedback form to specific  articles (post, page, custom post type), then you can use the deployment section within your Mopinion account: https://app.mopinion.com/survey/manage#tab_deployment_pane.

## Works or broken?

Please, consider to vote for this plugin. When you vote for broken, be so kind and tell in [email](support@mopinion.com) what is broken. Maybe we might be able to fix it to make the plugin also work for you.

## We need your support

It is very hard to continue development and support for this free extension without contributions from users like you. If you enjoy using our extension and find it useful, please consider for registering a paid version in your Mopinion account: https://app.mopinion.com/account/billing. Your paid plan will help encourage and support the extension's continued development and better user support. Also it offers many additional features such as advanced dashboarding/reporting and text analytics.

## Features

* Feedback forms: Improve the performance and user experience of your website with fully customisable feedback forms.
* Visual user feedback: Collect visual feedback (screenshots) to understand what your visitors want and what’s preventing them from achieving it.
* Feedback form triggering: Ask the right questions at the right moment to truly understand why your visitors are not converting…
* Reporting & analysis: Take full control of your feedback data visualisations and analyses.
* Text and sentiment analytics: Our native text analysis technology helps you explore huge amounts of data.
* Collaborate & Engage: We bring you from insights to action.


## Installation
Installation of this plugin is fairly easy as any other Magento plugin.

### Composer
In your Magento 2 root directory run
```
composer require mopinion/mopinion-feedback-survey
bin/magento module:enable Mopinion_FeedbackSurvey
bin/magento setup:upgrade
```
### Configuration
The configuration can be found in the Magento 2 admin panel under
Stores -> Mopinion Feedback Survey

### Dev installation
Please do following steps from root folder.

```
> cd app/code && mkdir Mopinion && cd Mopinion;
> git clone git@github.com:mopinion/Magento-mopinion.git FeedbackSurvey
```

Go back to root folder and run following commands
```
> bin/magento module:enable Mopinion_FeedbackSurvey
> bin/magento setup:upgrade && bin/magento module:status
> sudo chmod -R 777 generated/ && sudo chmod -R 777 var/ && sudo chmod -R 777 pub/static/ && sudo chmod -R 777 setup/ && sudo chmod -R 777 pub/media/ && sudo chmod -R 777 app/etc/
> bin/magento cache:clean
```

## Frequently Asked Questions

### Why another feedback survey extension?

Mopinion is not just another Magento extension... Mopinion is a rapidly growing SaaS provider and market leader in the field of online customer feedback analysis for websites and apps. Our solution turns feedback from digital touch-points into actionable data to drive strategy, improve online channels and empower digital teams. Mopinion is a partner of major national and international players such as Microsoft, Toshiba, KIA, TomTom, TUI, Daily Mail and BMW.

## Screenshots

1. Feedback Forms: By adding Mopinion to your site, you enable users to give feedback in a user-friendly way.
2. Visual Feedback: Allow users to select page elements that they want to provide feedback on. This page element is then submitted as a screenshot together with all other feedback.
3. Feedback inbox: Easily browse through all your feedback items with our intuitive feedback inbox. Search, filter and organise your feedback.
4. Easy setup and full control: Build your own feedback forms with various question elements, such as open comments and scores (including NPS and Csat).
5. Complete customisation of look and feel: Customise the complete look and feel of your feedback forms and buttons to give your visitors a fully branded experience.

## Upgrade Notice
Not applicable.

## App Details

Contributors: Mopinion.com
Donate link: https://mopinion.com

Tags: customer feedback, user feedback, website feedback, visual feedback, screenshot feedback, feedback button, feedback form, feedback widget, forms, user experience

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
