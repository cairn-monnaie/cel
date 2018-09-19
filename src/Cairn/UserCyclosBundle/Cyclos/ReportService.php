<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/ReportService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ReportService extends Service {

    function __construct() {
        parent::__construct('reportService');
    }
    
    /**

     * @return Java type: org.cyclos.model.system.reports.SystemReportData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/ReportService.html#getSystemReportData()
     */
    public function getSystemReportData() {
        return $this->__run('getSystemReportData', array());
    }
    
    /**
     * @param query Java type: org.cyclos.model.system.reports.SystemReportQuery
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/ReportService.html#readSystemReport(org.cyclos.model.system.reports.SystemReportQuery)
     */
    public function readSystemReport($query) {
        return $this->__run('readSystemReport', array($query));
    }
    
    /**
     * @param query Java type: org.cyclos.model.system.reports.UserReportQuery
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/ReportService.html#readUserReport(org.cyclos.model.system.reports.UserReportQuery)
     */
    public function readUserReport($query) {
        return $this->__run('readUserReport', array($query));
    }
    
    /**
     * @param query Java type: org.cyclos.model.system.reports.SystemReportQuery
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/ReportService.html#validate(org.cyclos.model.system.reports.SystemReportQuery)
     */
    public function validate($query) {
        $this->__run('validate', array($query));
    }
    
}

?>