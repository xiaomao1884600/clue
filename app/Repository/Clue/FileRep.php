<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 下午9:12
 */

namespace App\Repository\Clue;


use App\Model\Clue\Attachments;
use App\Repository\Foundation\BaseRep;

class FileRep extends BaseRep
{
    protected $attachments;
    public function __construct(
        Attachments $attachments
    )
    {
        parent::__construct();
        $this->attachments = $attachments;
    }

    public function saveAttachments(array $data)
    {
        return $this->attachments->saveTable($data);
    }
}