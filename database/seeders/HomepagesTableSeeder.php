<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HomepagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('homepages')->delete();
        
        \DB::table('homepages')->insert(array (
            0 => 
            array (
                'id' => 1,
                'about_image_1' => '',
                'about_title' => 'We Provide Best Nutrition Health Solution in Town',
                'about_description' => '<p>we are committed to offering personalized and effective nutrition health solutions tailored to your unique needs. Our team of expert dieticians takes the time to understand your health goals and design a plan that supports your overall well-being, whether it is weight management, improved energy, or managing a specific health condition.</p><p>With years of experience and a passion for nutrition, we provide evidence-based guidance that helps you make informed choices for a healthier lifestyle. Whether you are looking to improve your eating habits or need support in achieving specific health objectives, we are here to guide you every step of the way.</p>',
                'about_button_text' => 'Read More',
                'about_button_url' => '#',
                'intro_app_image_1' => 'default/d2.png',
                'intro_app_image_2' => 'default/d3.png',
                'intro_app_image_3' => 'default/d4.png',
                'intro_app_title' => 'Introducing the Dietician app',
                'intro_app_description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'intro_app_button_text' => 'GET THE APP',
                'intro_app_button_url' => '#',
                'contact_image_1' => 'default/u1.jpg',
                'contact_title' => '',
                'contact_description' => '',
                'slider_heading_1' => 'Healthy Food& Nutrition Gives You Good Life.',
                'slider_heading_2' => 'Download Our App',
                'slider_heading_3' => 'Available on Andriod and IOS',
                'slider_image_1' => '',
                'slide_image_1' => 'default/d1.png',
                'slide_image_2' => 'default/d2.png',
                'slide_image_3' => 'default/d3.png',
                'slide_image_4' => 'default/d4.png',
                'slide_image_5' => 'default/d1.png',
                'slide_image_6' => 'default/d2.png',
                'service' => '',
                'testimonial' => '',
                'our_clients_text' => NULL,
                'our_clients_image' => NULL,
                'our_packages_text' => 'Discover our personalized nutrition packages, crafted by expert dieticians to support your health goals. Whether it\'s weight loss, improved energy, or managing a condition, we have the perfect plan for you.',
                'features' => NULL,
                'packages' => '',
                'created_at' => '2025-08-07 10:41:53',
                'updated_at' => '2025-08-07 10:41:53',
            ),
        ));
        
        
    }
}