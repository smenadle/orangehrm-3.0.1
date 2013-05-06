<?php

/**
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
*/

class AddCandidateMailer extends orangehrmRecruitmentMailer {

    public function  __construct($performerId, $candidateId, $vacancyId) {

        parent::__construct();

        $this->performer = $this->getEmployeeService()->getEmployee($performerId);
        $this->candidate = $this->getCandidateService()->getCandidateById($candidateId);
        $this->vacancy = $this->getVacancyService()->getVacancyById($vacancyId);
    }

    public function sendToAdmin() {
	    $adminUsers = $this->getSystemUserService()->getAdminSystemUsers();
	    if (count($adminUsers) > 0) {
		    foreach ($adminUsers as $admin) {
			    $recipient = $admin->getEmployee();
			    if (!empty($recipient)) {
				    try {
					    $recipientName = $recipient->getEmpWorkEmail();
					    $this->message->setFrom($this->getSystemFrom());
					    $this->message->setTo($recipientName);
					    
					    $message = new AddCandidateMailContent($this->performer, $recipient, $this->candidate, $this->vacancy);
					    
					    $this->message->setSubject($message->generateSubject());
					    $this->message->setBody($message->generateBody());
					    
					    $this->mailer->send($this->message);
					    
					    $logMessage = "Candidate registration email was sent to $recipientName ";
					    $this->logResult('Success', $logMessage);
				    } catch (Exception $e) {
					    
					    $logMessage = "Couldn't send candidate registration subscription email to $recipientName";
					    $logMessage .= '. Reason: ' . $e->getMessage();
					    $this->logResult('Failure', $logMessage);
				    }
			    }
		    }
	    }
    }

    public function sendToHiringManager() {
	    $recipient = $this->vacancy->getHiringManager();
	    
	    if (!empty($recipient)) {
		    try {
			    
			    $recipientName = $recipient->getEmpWorkEmail();
			    $this->message->setFrom($this->getSystemFrom());
			    $this->message->setTo($recipientName);
			    
			    $message = new AddCandidateMailContent($this->performer, $recipient, $this->candidate, $this->vacancy);
			    
			    $this->message->setSubject($message->generateSubject());
			    $this->message->setBody($message->generateBody());
			    
			    $this->mailer->send($this->message);
			    
			    $logMessage = "Candidate registration email was sent to $recipientName";
			    $this->logResult('Success', $logMessage);
		    } catch (Exception $e) {
			    
			    $logMessage = "Couldn't send candidate registration subscription email to $recipientName";
			    $logMessage .= '. Reason: ' . $e->getMessage();
			    $this->logResult('Failure', $logMessage);
		    }
	    }
	    
    }
   
    public function send() {

        if (!empty($this->mailer)) {
        	$this->sendToAdmin();
            $this->sendToHiringManager();
        }
    }

}