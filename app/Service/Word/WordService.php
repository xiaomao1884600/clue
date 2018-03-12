<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/8
 * Time: 下午3:38
 */
namespace App\Service\Word;

use App\Service\Foundation\BaseService;
use PhpOffice\PhpWord\PhpWord;

class WordService extends BaseService
{
    protected $phpWord;

    protected $tempData = [
        'title' => '测试文档',
        'name' => '张三',
        'number' => '0100016',
        'mobile' => '15100120034',
        'address' => 'http://map.baidu.com',
        'describe' => '这样做其实也对着了，看着确实是把utf-8转化为gb2312了，但是实际运行的话，往往都是以失败告终的，原因呢？ 

原因实际上也很简单，因为任何的函数都是执行错误的时候，同时很不幸的是iconv();就很终于出现错误。现在给你正确的答案。 
',
        'text' => '没有什么比这个更先进 ！',
        'image' => [
            'link' => 'a.jpg',
            'title' => '风景',
        ],
        'link' => [
            'link' => 'http://pan.baidu.com',
            'title' => '百度地图',
        ],
    ];

    public function __construct(
        PhpWord $phpWord
    )
    {
        $this->phpWord = $phpWord;
    }

    public function exportWord(array $params)
    {
        // TODO 获取数据及模板信息
        $data = $this->tempData;

        // 生成word
        // 字体
        $this->phpWord->setDefaultFontName('仿宋');

        // 字号
        $this->phpWord->setDefaultFontSize('16');

        // 添加页面
        $section = $this->phpWord->addSection();

        // 添加标题
        $this->phpWord->addTitleStyle(1, ['bold' => true, 'color' => '1BFF32', 'size' => 38, 'name' => 'Verdana']);
        $section->addTitle($data['title'], 1);

        // 添加文本
        $section->addText($data['text']);

        // 换行
        $section->addTextBreak();

        // 添加图片
        $imageStyle = ['width' => 480, 'height' => 640, 'align' => 'center'];
        $section->addImage(public_path($data['image']['link']), $imageStyle);

        // 超链接
        $linkStyle = ['color' => '0000FF', 'underline' => \PhpOffice\PhpWord\Style\Font::UNDERLINE_SINGLE];
        $this->phpWord->addLinkStyle('clueLinkStyle', $linkStyle);
        $section->addLink($data['link']['link'], $data['link']['title'], 'clueLinkStyle');

        // 添加页眉页脚
        $header = $section->createHeader();
        $footer = $section->createFooter();
        $header->addPreserveText('页眉');
        $footer->addPreserveText('页脚 - 页数 {PAGE} - {NUMPAGES}.');

        // 生成word文档
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($this->phpWord, 'Word2007');
        $writer->save(public_path('./test.docx'));


        // 转换word为html
//        $reader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
//        $objFile = $reader->load(public_path('./test.docx'));
//        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($objFile, 'HTML');
//        $writer->save(public_path('./test2.html'));

        return [];
    }

    public function exportTemp(array $params)
    {
        $data = $this->tempData;

        // 载入模板
        $document = $this->phpWord->loadTemplate(public_path('./clue_temp_bk.docx'));

        // 替换模板内容
        $document->setValue('title', $data['title']);
        $document->setValue('name', $data['name']);
        $document->setValue('number', $data['number']);
        $document->setValue('describe', $data['describe']);
        $document->setValue('mobile', $data['mobile']);
        $document->setValue('address', $data['address']);

        // 保存新的word文档

        $result = $document->saveAs(public_path('./clue_new.docx'));

        // 读取word文档
        $reader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        $objFile = $reader->load(public_path('./clue_new.docx'));

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($objFile, 'HTML');

        $writer->save(public_path('./clue_new.html'));

        return [$result];
    }
}