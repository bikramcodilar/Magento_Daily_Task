<?php
namespace Codilar\Faq\Block;

use Magento\Framework\View\Element\Template;
class Faq extends Template
{
    public function getFaqs(): array
    {
        return [
            [
                'question' => 'What is Magento?',
                'answer' => 'Magento is an open-source e-commerce platform used to build online stores.',
                'category' => 'Magento',
            ],
            [
                'question' => 'What is a CMS Page?',
                'answer' => 'A CMS Page is an admin-managed page that can contain content such as text, images and links.',
                'category' => 'CMS',
            ],
            [
                'question' => 'What is a CMS Block?',
                'answer' => 'A CMS Block is reusable content that can be managed from the Magento Admin Panel.',
                'category' => 'CMS',
            ],
            [
                'question' => 'What is a Magento Block?',
                'answer' => 'A Magento Block is a PHP class that provides data or logic to a template.',
                'category' => 'Development',
            ],
            [
                'question' => 'What is Layout XML?',
                'answer' => 'Layout XML controls the structure and placement of blocks and containers on a Magento page.',
                'category' => 'Development',
            ],
            [
                'question' => 'What is a Magento Theme?',
                'answer' => 'A Magento theme controls the visual presentation of the storefront, including templates, CSS and JavaScript.',
                'category' => 'Frontend',
            ],
            [
                'question' => 'What is a Child Theme?',
                'answer' => 'A child theme allows you to customize an existing parent theme without modifying the parent theme directly.',
                'category' => 'Frontend',
            ],
            [
                'question' => 'What is Dependency Injection?',
                'answer' => 'Dependency Injection allows Magento to provide the dependencies required by a class instead of creating them manually.',
                'category' => 'Development',
            ],
        ];
    }
    public function getCategories(): array
    {
        $categories = [];
        foreach ($this->getFaqs() as $faq) {
            $categories[] = $faq['category'];
        }
        return array_unique($categories);
    }
}
