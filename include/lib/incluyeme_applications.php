<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/wp-load.php';


class incluyeme_applications
{
    protected static ?string $candidate;
    protected static ?string $candidateMail;
    protected static ?string $candidateKey;
    private static wpdb $wp;
    private static string $incluyemeFilters;
    private static $prefix;
    
    public function __construct()
    {
        global $wpdb;
        self::$wp = $wpdb;
        self::$candidate = null;
        self::$candidateMail = null;
        self::$candidateKey = null;
        self::$incluyemeFilters = 'incluyemeFiltersCV';
        self::$prefix = self::$wp->prefix;
    }
    
    public function json_response($code = 200, $message = null)
    {
        // clear the old headers
        header_remove();
        // set the actual code
        http_response_code($code);
        // set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        // treat this as json
        header('Content-Type: application/json; charset=utf-8');
        $status = [
            200 => '200 OK',
            400 => '400 Bad Request',
            422 => 'Unprocessable Entity',
            500 => '500 Internal Server Error'
        ];
        // ok, validation error, or failure
        header('Status: ' . $status[$code]);
        // return the encoded json
        return json_encode([
            'status' => $code < 300, // success or not?
            'message' => $message
        ]);
    }
    
    public function searchCandidate()
    {
        $query = "";
        $prefix = self::$prefix;
        if (self::getCandidate()) {
            $candidate = self::getCandidate();
            $query = "SELECT   {$prefix}users.ID            AS users_id,
       {$prefix}users.user_email,
         {$prefix}users.display_name,
         {$prefix}wpjb_resume.phone,
         {$prefix}posts.guid,
         {$prefix}usermeta.meta_value AS first_name,
         {$prefix}usermeta.meta_key,
         {$prefix}wpjb_resume.candidate_state,
         {$prefix}wpjb_resume.candidate_location,
         {$prefix}wpjb_resume.id      AS resume_id,
       lVal.meta_value        AS last_name
                FROM   {$prefix}wpjb_resume
                         LEFT JOIN   {$prefix}users
                                   ON   {$prefix}users.ID = {$prefix}wpjb_resume.user_id
                         LEFT JOIN   {$prefix}wpjb_application
                                   ON   {$prefix}wpjb_resume.user_id = {$prefix}wpjb_application.user_id
                         LEFT JOIN   {$prefix}wpjb_job
                                   ON   {$prefix}wpjb_application.job_id = {$prefix}wpjb_job.id
                         LEFT OUTER JOIN   {$prefix}posts
                                         ON   {$prefix}wpjb_resume.post_id = {$prefix}posts.ID
                         INNER JOIN   {$prefix}usermeta
                                    ON   {$prefix}users.ID = {$prefix}usermeta.user_id
                                        AND   {$prefix}usermeta.meta_key = 'first_name'
                         LEFT OUTER JOIN   {$prefix}wpjb_company
                                         ON   {$prefix}wpjb_job.employer_id =   {$prefix}wpjb_company.id
                         LEFT OUTER JOIN   {$prefix}usermeta lVal
                                         ON   {$prefix}users.ID = lVal.user_id
                                             AND lVal.meta_key = 'last_name'
                                             WHERE  {$prefix}usermeta.meta_value Like '{$candidate}' OR lVal.meta_value  Like '{$candidate}'";
        } else if (self::getCandidateKey()) {
            $key = self::getCandidateKey();
            $query = "SELECT {$prefix}users.ID AS users_id,
       {$prefix}users.user_email,
         {$prefix}users.display_name,
         {$prefix}wpjb_resume.phone,
         {$prefix}posts.guid,
         {$prefix}usermeta.meta_value AS first_name,
         {$prefix}usermeta.meta_key,
         {$prefix}wpjb_resume.candidate_state,
         {$prefix}wpjb_resume.candidate_location,
         {$prefix}wpjb_resume.id      AS resume_id,
       lVal.meta_value        AS last_name
                FROM   {$prefix}wpjb_resume
                         LEFT JOIN   {$prefix}users
                                   ON   {$prefix}users.ID = {$prefix}wpjb_resume.user_id
                         LEFT JOIN   {$prefix}wpjb_application
                                   ON   {$prefix}wpjb_resume.user_id = {$prefix}wpjb_application.user_id
                         LEFT JOIN   {$prefix}wpjb_job
                                   ON   {$prefix}wpjb_application.job_id = {$prefix}wpjb_job.id
                         LEFT OUTER JOIN   {$prefix}posts
                                         ON   {$prefix}wpjb_resume.post_id = {$prefix}posts.ID
                         INNER JOIN   {$prefix}usermeta
                                    ON   {$prefix}users.ID = {$prefix}usermeta.user_id
                                        AND   {$prefix}usermeta.meta_key = 'first_name'
                         LEFT OUTER JOIN   {$prefix}wpjb_company
                                         ON   {$prefix}wpjb_job.employer_id =   {$prefix}wpjb_company.id
                         LEFT OUTER JOIN   {$prefix}usermeta lVal
                                         ON   {$prefix}users.ID = lVal.user_id
                                             AND lVal.meta_key = 'last_name'
                                             WHERE  {$prefix}users.user_email Like '{$key}'";
            
            $query .= ' OR ' . $prefix . 'usermeta.meta_value Like  "%' . $key . '%" ';
            $query .= ' OR ' . $prefix . 'wpjb_application.status Like "%' . $key . '%" ';
            $query .= ' OR ' . $prefix . 'wpjb_resume.candidate_state Like "%' . $key . '%" ';
            $query .= ' OR ' . $prefix . 'wpjb_resume.candidate_location Like "%' . $key . '%" ';
            $query .= ' OR ' . $prefix . 'usermeta.meta_value  Like "%' . $key . '%" ';
            $query .= ' OR ' . $prefix . 'users.user_email Like "%' . $key . '%"  ';
            $query .= " OR {$prefix}wpjb_resume.id IN (SELECT
                      {$prefix}wpjb_resume_detail.resume_id
                    FROM {$prefix}wpjb_resume_detail
                      INNER JOIN {$prefix}wpjb_resume
                            ON {$prefix}wpjb_resume_detail.resume_id = {$prefix}wpjb_resume.id
                          INNER JOIN {$prefix}wpjb_application
                            ON {$prefix}wpjb_resume.user_id = {$prefix}wpjb_application.user_id
                          INNER JOIN {$prefix}wpjb_job
                            ON {$prefix}wpjb_application.job_id = {$prefix}wpjb_job.id
                          INNER JOIN {$prefix}wpjb_company
                            ON {$prefix}wpjb_job.employer_id = {$prefix}wpjb_company.id
                            WHERE ({$prefix}wpjb_resume_detail.detail_title LIKE '%" . $key . "%'
                      OR   {$prefix}wpjb_resume_detail.grantor LIKE  '%" . $key . "%' OR 
                        {$prefix}wpjb_resume_detail.detail_description  LIKE  '%" . $key . "%') ) ";
        } else if (self::getCandidateMail()) {
            $mail = self::getCandidateMail();
            $query = "SELECT   {$prefix}users.ID            AS users_id,
       {$prefix}users.user_email,
         {$prefix}users.display_name,
         {$prefix}wpjb_resume.phone,
         {$prefix}posts.guid,
         {$prefix}usermeta.meta_value AS first_name,
         {$prefix}usermeta.meta_key,
         {$prefix}wpjb_resume.candidate_state,
         {$prefix}wpjb_resume.candidate_location,
         {$prefix}wpjb_resume.id      AS resume_id,
       lVal.meta_value        AS last_name
                FROM   {$prefix}wpjb_resume
                         LEFT JOIN   {$prefix}users
                                   ON   {$prefix}users.ID = {$prefix}wpjb_resume.user_id
                         LEFT JOIN   {$prefix}wpjb_application
                                   ON   {$prefix}wpjb_resume.user_id = {$prefix}wpjb_application.user_id
                         LEFT JOIN   {$prefix}wpjb_job
                                   ON   {$prefix}wpjb_application.job_id = {$prefix}wpjb_job.id
                         LEFT OUTER JOIN   {$prefix}posts
                                         ON   {$prefix}wpjb_resume.post_id = {$prefix}posts.ID
                         INNER JOIN   {$prefix}usermeta
                                    ON   {$prefix}users.ID = {$prefix}usermeta.user_id
                                        AND   {$prefix}usermeta.meta_key = 'first_name'
                         LEFT OUTER JOIN   {$prefix}wpjb_company
                                         ON   {$prefix}wpjb_job.employer_id =   {$prefix}wpjb_company.id
                         LEFT OUTER JOIN   {$prefix}usermeta lVal
                                         ON   {$prefix}users.ID = lVal.user_id
                                             AND lVal.meta_key = 'last_name'
                                             WHERE  {$prefix}usermeta.user_email Like '{$mail}'";
        } else {
            return [];
        }
        return self::getCV(self::executeQueries($query));
    }
    
    /**
     * @return string|null
     */
    public static function getCandidate(): ?string
    {
        return self::$candidate;
    }
    
    /**
     * @param string|null $candidate
     */
    public static function setCandidate(?string $candidate): void
    {
        self::$candidate = $candidate;
    }
    
    /**
     * @return string|null
     */
    public static function getCandidateKey(): ?string
    {
        return self::$candidateKey;
    }
    
    /**
     * @param string|null $candidateKey
     */
    public static function setCandidateKey(?string $candidateKey): void
    {
        self::$candidateKey = $candidateKey;
    }
    
    /**
     * @return string|null
     */
    public static function getCandidateMail(): ?string
    {
        return self::$candidateMail;
    }
    
    /**
     * @param string|null $candidateMail
     */
    public static function setCandidateMail(?string $candidateMail): void
    {
        self::$candidateMail = $candidateMail;
    }
    
    protected function getCV($obj)
    {
        
        $CVS = get_option(self::$incluyemeFilters) ? get_option(self::$incluyemeFilters) : 'certificado-discapacidad';
        $path = wp_upload_dir();
        $basePath = $path['basedir'];
        $baseDir = $path['baseurl'];
        for ($i = 0; $i < count($obj); $i++) {
            $route = $basePath . '/wpjobboard/resume/' . $obj[$i]->resume_id;
            $dir = $baseDir . '/wpjobboard/resume/' . $obj[$i]->resume_id;
            if (file_exists($route)) {
                if (file_exists($route . '/cv/')) {
                    $folder = @scandir($route . '/cv/');
                    if (count($folder) > 2) {
                        $search = opendir($route . '/cv/');
                        while ($file = readdir($search)) {
                            if ($file != "." and $file != ".." and $file != "index.php") {
                                $obj[$i]->CV = $dir . '/cv/' . $file;
                            }
                        }
                    } else {
                        $obj[$i]->CV = false;
                    }
                } else {
                    $obj[$i]->CV = false;
                }
                if (file_exists($route . '/image/')) {
                    $folder = @scandir($route . '/image/');
                    if (count($folder) > 2) {
                        $search = opendir($route . '/image/');
                        while ($file = readdir($search)) {
                            if ($file != "." and $file != ".." and $file != "index.php") {
                                $obj[$i]->img = $dir . '/image/' . $file;
                            }
                        }
                    } else {
                        $obj[$i]->img = false;
                    }
                } else {
                    $obj[$i]->img = false;
                }
                if (file_exists($route . '/' . $CVS . '/')) {
                    $folder = @scandir($route . '/' . $CVS . '/');
                    if (count($folder) > 2) {
                        $search = opendir($route . '/' . $CVS . '/');
                        while ($file = readdir($search)) {
                            if ($file != "." and $file != ".." and $file != "index.php") {
                                $obj[$i]->CUD = $dir . '/' . $CVS . '/' . $file;
                            }
                        }
                    } else {
                        $obj[$i]->CUD = false;
                    }
                } else {
                    $obj[$i]->CUD = false;
                }
            } else {
                $obj[$i]->img = false;
                $obj[$i]->CUD = false;
                $obj[$i]->CV = false;
            }
        }
        return $obj;
    }
    
    protected function executeQueries($sql)
    {
        return self::$wp->get_results($sql);
    }
}