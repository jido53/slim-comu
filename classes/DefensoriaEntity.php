<?php
class DefensoriaEntity
{

    //org_id, org_descr, org_mail from org where org_clase = 'D' and org_id between 177 and 203 order by org_id;
    protected $org_id;
    protected $org_descr;
    protected $org_mail;
    protected $org_clase;
    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no org_id if we're creating
        if(isset($data['org_id'])) {
            $this->org_id = $data['org_id'];
        }
        $this->org_descr = $data['org_descr'];
        $this->org_mail = $data['org_mail'];
        $this->org_clase = $data['org_clase'];
    }
    public function getOrg_id() {
        return $this->org_id;
    }
    public function getOrg_descr() {
        return $this->org_descr;
    }
    public function getOrg_mail() {
        return $this->org_mail;
    }
    public function getShortOrg_mail() {
        return substr($this->org_mail, 0, 20);
    }
    public function getOrg_clase() {
        return $this->org_clase;
    }
}