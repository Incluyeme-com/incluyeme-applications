<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/wp-load.php';


class incluyeme_applications
{
    protected static ?string $candidate;
    protected static ?string $candidateMail;
    protected static ?string $candidateKey;
    protected static ?string $jobId;
    protected static ?string $employed;
    protected static ?string $job;
    protected static ?string $message;
    protected static ?string $applicationMessage;
    protected static array $candidates;
    protected static array $jobs;
    private static wpdb $wp;
    private static string $incluyemeFilters;
    private static ?string $prefix;
    public ?int $resultsNumbers = 1;
    
    public function __construct()
    {
        global $wpdb;
        self::$wp = $wpdb;
        self::$candidate = null;
        self::$candidateMail = null;
        self::$candidateKey = null;
        self::$message = null;
        
        self::$candidates = [];
        self::$jobs = [];
        self::$applicationMessage = null;
        self::$job = null;
        self::$employed = null;
        self::$jobId = null;
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
            $candidate = "%" . self::getCandidate() . "%";
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
                                             WHERE 
                                              {$prefix}usermeta.meta_value Like '{$candidate}' 
                                              OR lVal.meta_value  Like '{$candidate}'
                                              GROUP BY {$prefix}wpjb_resume.id";
        } else if (self::getCandidateKey()) {
            $key = "%" . self::getCandidateKey() . "%";
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
                        {$prefix}wpjb_resume_detail.detail_description  LIKE  '%" . $key . "%') ) 
                        GROUP BY {$prefix}wpjb_resume.id";
        } else if (self::getCandidateMail()) {
            $mail = "%" . self::getCandidateMail() . "%";
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
                                             WHERE  {$prefix}users.user_email Like '{$mail}'
                                             GROUP BY {$prefix}wpjb_resume.id";
        } else {
            return [];
        }
        return self::getCV($this->paginatedQueries($query));
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
    
    public function paginatedQueries($sql): array
    {
        $resultNumber = $this->resultsNumbers;
        $LIMITQuery = ($resultNumber - 1) * 10 ?: 0;
        $sql .= " LIMIT {$LIMITQuery}, 10";
        error_log(print_r($sql, true));
        return self::executeQueries($sql);
    }
    
    protected function executeQueries($sql)
    {
        return self::$wp->get_results($sql);
    }
    
    public function searchEmployee()
    {
        $prefix = self::$prefix;
        $where = " WHERE t1.is_active = 1
AND t1.job_created_at <= NOW()
AND t1.job_expires_at >= NOW()
AND is_filled != 3
AND is_filled = 0 ";
        $query = "SELECT   t1.id,
  t1.employer_id,
  t1.post_id,
  t1.job_title,
  t1.company_name,
  {$prefix}wpjb_company.company_name AS company
FROM `{$prefix}wpjb_job` AS `t1`


  LEFT JOIN {$prefix}wpjb_company
    ON t1.employer_id = {$prefix}wpjb_company.id
";
        if (self::getJob()) {
            $job = "%" . self::getJob() . "%";
            $where .= " AND (t1.job_title LIKE '{$job}' OR 
        t1.job_description LIKE '{$job}' OR 
        t1.job_slug LIKE '{$job}' )";
        } else if (self::getJobId()) {
            $job = self::getJobId();
            $where .= "AND t1.job.id  =  '{$job}'";
        } else if (self::getEmployed()) {
            $job = "%" . self::getEmployed() . "%";
            $where .= " AND ( t1.company_name LIKE '{$job}' OR 
        {$prefix}wpjb_company.company_name LIKE '{$job}' OR 
        t1.job_slug LIKE '{$job}' OR
         {$prefix}wpjb_company.company_slogan LIKE '{$job}' OR 
  {$prefix}wpjb_company.company_info LIKE '{$job}' )
        ";
        }
        $query = $query . $where . " GROUP BY t1.id ORDER BY t1.is_featured DESC, t1.job_created_at DESC, t1.id DESC ";
        error_log(print_r($this->paginatedQueries($query), true));
        return $this->paginatedQueries($query);
    }
    
    /**
     * @return string|null
     */
    public static function getJob(): ?string
    {
        return self::$job;
    }
    
    /**
     * @param string|null $job
     */
    public static function setJob(?string $job): void
    {
        self::$job = $job;
    }
    
    /**
     * @return string|null
     */
    public static function getJobId(): ?string
    {
        return self::$jobId;
    }
    
    /**
     * @param string|null $jobId
     */
    public static function setJobId(?string $jobId): void
    {
        self::$jobId = $jobId;
    }
    
    /**
     * @return string|null
     */
    public static function getEmployed(): ?string
    {
        return self::$employed;
    }
    
    /**
     * @param string|null $employed
     */
    public static function setEmployed(?string $employed): void
    {
        self::$employed = $employed;
    }
    
    public function appApplications(): bool
    {
        global $wpdb;
        $prefix = self::$prefix;
        $whereIn = '"' . implode('","', self::getCandidates()) . '"';
        $whereInJobs = '"' . implode('","', self::getJobs()) . '"';
        $candidatesEmail = $query = "SELECT   {$prefix}users.ID            AS users_id,
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
                                             WHERE {$prefix}users.ID  in ($whereIn)
                                              GROUP BY {$prefix}wpjb_resume.id ";
        $candidatesEmail = self::executeQueries($candidatesEmail);
        
        $candidatesJobs = $query = "SELECT   * 
                FROM   {$prefix}wpjb_job WHERE {$prefix}wpjb_job.id  in ($whereInJobs)
                                              GROUP BY {$prefix}wpjb_job.id ";
        $candidatesJobs = self::executeQueries($candidatesJobs);
        for ($i = 0; $i < count($candidatesEmail); $i++) {
            for ($j = 0; $j < count($candidatesJobs); $j++) {
                error_log(print_r($candidatesJobs, true));
                self::$wp->insert($prefix . "wpjb_application", [
                    "job_id" => $candidatesJobs[$j]->id,
                    "user_id" => $candidatesEmail[$i]->users_id,
                    "applied_at" => date("Y-m-d H:i:s", time()),
                    "applicant_name" => $candidatesEmail[$i]->first_name . " " . $candidatesEmail[$i]->last_name,
                    "message" => self::getApplicationMessage(),
                    "email" => $candidatesEmail[$i]->user_email,
                    "status" => 1
                ]);
            }
            if (self::getApplicationMessage()) {
                wp_mail($candidatesEmail[$i]->user_email, 'Ha aplicado exitosamente', self::getMessage());
            }
            
        }
        return true;
    }
    
    /**
     * @return array
     */
    public static function getCandidates(): array
    {
        return self::$candidates;
    }
    
    /**
     * @param array $candidates
     */
    public static function setCandidates(array $candidates): void
    {
        self::$candidates = $candidates;
    }
    
    /**
     * @return array
     */
    public static function getJobs(): array
    {
        return self::$jobs;
    }
    
    /**
     * @param array $jobs
     */
    public static function setJobs(array $jobs): void
    {
        self::$jobs = $jobs;
    }
    
    /**
     * @return string|null
     */
    public static function getApplicationMessage(): ?string
    {
        return self::$applicationMessage;
    }
    
    /**
     * @param string|null $applicationMessage
     */
    public static function setApplicationMessage(?string $applicationMessage): void
    {
        self::$applicationMessage = $applicationMessage;
    }
    
    /**
     * @return string|null
     */
    public static function getMessage(): ?string
    {
        return self::$message;
    }
    
    /**
     * @param string|null $message
     */
    public static function setMessage(?string $message): void
    {
        self::$message = $message;
    }
}