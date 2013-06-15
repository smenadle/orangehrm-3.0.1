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

class InterviewMailContent extends orangehrmRecruitmentMailContent {
	
	protected $actionStatus;
	protected $interviewer;
	protected $jobInterview;
	protected $subjectTemplateName;	
	protected $bodyTemplateName;
	protected $interviewerName;

	public function  __construct($performer, $recipient, $candidate, $vacancy, $action, $jobInterview, $interviewerName) {
		
		$this->performer = $performer;
		$this->recipient = $recipient;
		$this->candidate=  $candidate;
		$this->vacancy = $vacancy;
		$this->action = $action;
		$this->jobInterview = $jobInterview;
		$this->interviewerName = $interviewerName;
		
		parent::__construct($this->performer, $this->recipient, $this->candidate, $this->vacancy );
		
		$this->populateInterview();
		$this->getTemplateNameByAction($this->action);
		
	}
	
	public function populateInterview() {
		if ($this->jobInterview instanceof JobInterview) {
			$this->replacements['interviewer'] = $this->interviewerName;
			$this->replacements['interviewType'] = $this->jobInterview->getInterviewName();
			$this->replacements['interviewDate'] = $this->jobInterview->getInterviewDate();
			$this->replacements['interviewTime'] = $this->jobInterview->getInterviewTime();
			$this->replacements['note'] = $this->jobInterview->getNote();
			
			//$this->replacements['mimeBoundary'] =  "----Meeting Booking----".md5(time());
			$this->replacements['calId'] =  date('Ymd').'T'.date('His')."-".rand()."@synerzipHRMS";
			date_default_timezone_set("Asia/Kolkata");
			$meeting_date = $this->jobInterview->getInterviewDate()." ".$this->jobInterview->getInterviewTime(); // "2010-07-06 13:40:00"; //mysql format
			$meetingstamp = strtotime($meeting_date);		 
			$this->replacements['dtstart'] =  gmdate("Ymd\THis\Z",$meetingstamp);
			$this->replacements['dtend'] =  gmdate("Ymd\THis\Z",$meetingstamp+3600);
			$this->replacements['todaystamp'] = gmdate("Ymd\THis\Z");
			
		}      
    } 

    public function getSubjectTemplate() {

        if (empty($this->subjectTemplate)) {
            $this->subjectTemplate = trim($this->readFile($this->templateDirectoryPath . $this->subjectTemplateName));
        }
        return $this->subjectTemplate;

    }

    public function getSubjectReplacements() {

        if (empty($this->subjectReplacements)) {

            $this->subjectReplacements = array('candidateName' => $this->replacements['candidateName'],
                                               'vacancyName' => $this->replacements['vacancyName']
                                               );
        }

        return $this->subjectReplacements;
        
    }

    public function getBodyTemplate() {
        if (empty($this->bodyTemplate)) {
            $this->bodyTemplate = $this->readFile($this->templateDirectoryPath . $this->bodyTemplateName);
        }
        return $this->bodyTemplate;
    }

    public function getBodyReplacements() {
        if (empty($this->bodyReplacements)) {
            if(empty($this->jobInterview)){
            	$this->bodyReplacements = array('recipientFirstName' => $this->replacements['recipientFirstName'],
                                            'candidateName' => $this->replacements['candidateName'],
                                            'vacancyName' => $this->replacements['vacancyName'],
                                            'synerzipHRMSite' => $this->replacements['synerzipHRMSite'],
                                            'synerzipHRMVacancySite' => $this->replacements['synerzipHRMVacancySite']
                                            );
            }else{
            	$this->bodyReplacements = array('recipientFirstName' => $this->replacements['recipientFirstName'],
                                            'candidateName' => $this->replacements['candidateName'],
                                            'performerEmail' => $this->replacements['performerEmail'],
                                            'vacancyName' => $this->replacements['vacancyName'],
                                            'interviewer' => $this->replacements['interviewer'],
                                            'interviewType' => $this->replacements['interviewType'],
                                            'interviewDate' => $this->replacements['interviewDate'],
                                            'interviewTime' => $this->replacements['interviewTime'],
                                            'calId' => $this->replacements['calId'],
                                            'dtstart' => $this->replacements['dtstart'],
                                            'dtend' => $this->replacements['dtend'],
                                            'todaystamp' => $this->replacements['todaystamp'],
                                            'note' => $this->replacements['note'],
                                            'synerzipHRMSite' => $this->replacements['synerzipHRMSite'],
                                            'synerzipHRMVacancySite' => $this->replacements['synerzipHRMVacancySite']
                                            );
            	
            }
        }
        return $this->bodyReplacements;
    }
    
    function scheduleMeeting() {
	    $from_name = $this->performer->getFirstName();
	    $from_address = trim($this->performer->getEmpWorkEmail());
	    $subject = "Interview Scheduled for ".$this->candidate->getFullName(); //Doubles as email subject and meeting subject in calendar
	    $meeting_description = $this->jobInterview->getInterviewName().". ".$this->interviewerName ." will take the interview\n\n";
	    $meeting_location = "Synerzip"; 
	    
	    //set interview time
	    $dtstart= $this->replacements['dtstart'];
	    $dtend= $this->replacements['dtend'];
	    $todaystamp = gmdate("Ymd\THis\Z");
	    
	    
	    //Create unique identifier
	    $cal_uid = date('Ymd').'T'.date('His')."-".rand()."@synerzipHRMS";
	    
	    //Create Mime Boundry
	    $mime_boundary = "----Meeting Booking----".md5(time());
	    //$body = $this->generateBody();
	    
	    //Create Email Headers
	    $headers = "From: ".$from_name." <".$this->performer->getEmpWorkEmail().">\n";
	    $headers .= "Reply-To: ".$from_name." <".$this->performer->getEmpWorkEmail().">\n";
	    
	    $headers .= "MIME-Version: 1.0\n";
	    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
	    $headers .= "Content-class: urn:content-classes:calendarmessage\n";
	    
	    //Create Email Body (HTML)
	    $message = "--$mime_boundary\n";
	    $message .= "Content-Type: text/html; charset=UTF-8\n";
	    $message .= "<html>\n";
	    $message .= "<body>\n";
	    $message .= "<p>Hi ".$this->recipient->getFirstName().",</p>";
	    $message .= "<p>Interview scheduled for <b>".$this->candidate->getFullName()."</b> shortlisted for <b>".$this->vacancy->getVacancyName()."</b> vacancy.</p>";   
	    $message .= "<p>Please find below the interview details</p>";  
	    $message .= "<p>Interviewer(s) Name : ".$this->interviewerName."<br>";    
	    $message .= "Interview Type 	    : ".$this->jobInterview->getInterviewName()."<br>"; 
	    $message .= "Date of Interview      : ".$this->jobInterview->getInterviewDate()."<br>"; 
	    $message .= "Time of Interview      : ".$this->jobInterview->getInterviewTime()."<br>"; 
	    $message .= "Note		            : ".$this->jobInterview->getNote()."</p>"; 
	    $message .= "</body>\n";
	    $message .= "</html>\n";
	    $message .= "--$mime_boundary\n";
	    
	    //Create ICAL Content (Google rfc 2445 for details and examples of usage) 
	    
	    $cal[] = 'BEGIN:VCALENDAR';
	    $cal[] = 'PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN';
	    $cal[] = 'VERSION:2.0';
	    $cal[] = 'METHOD:REQUEST';
	    $cal[] = 'BEGIN:VEVENT';
	    $cal[] = 'ORGANIZER:MAILTO:hrmsadmin@synerzip.com';
	    $cal[] = 'DTSTART:'.$dtstart.'';
	    $cal[] = 'DTEND:'.$dtend.'';
	    $cal[] = 'LOCATION:'.$meeting_location.'';
	    $cal[] = 'TRANSP:OPAQUE';
	    $cal[] = 'SEQUENCE:0';
	    $cal[] = 'UID:'.$cal_uid.'';
	    $cal[] = 'DTSTAMP:'.$todaystamp.'';
	    $cal[] = 'DESCRIPTION:'.$meeting_description.'';
	    $cal[] = 'SUMMARY:'.$subject.'';
	    $cal[] = 'PRIORITY:5';
	    $cal[] = 'CLASS:PUBLIC';
	    $cal[] = 'BEGIN:VALARM';
	    $cal[] = 'TRIGGER:-PT15M';
	    $cal[] = 'ACTION:DISPLAY';
	    $cal[] = 'DESCRIPTION:Reminder';
	    $cal[] = 'END:VALARM';
	    $cal[] = 'END:VEVENT';
	    $cal[] = 'END:VCALENDAR';
	    $cal_str = implode("\r\n",  $cal);
	    
	    $message .= "Content-Type: text/calendar;name=\"meeting.ics\";method=REQUEST;charset=utf-8\n";
	    $message .= "Content-Transfer-Encoding: 8bit\n\n";
	    $message .= $cal_str;        
	    
	    $recipientEmail = $this->recipient->getEmpWorkEmail();
	    //SEND MAIL
	    $mail_sent = @mail($recipientEmail, $subject, $message, $headers );
	    
	    if($mail_sent)     {
		    $logMessage = "Meeting request mail sent to $recipientEmail";
		    $this->logResult('Success', $logMessage);
	    } else {
		    $logMessage = "Couldn't send meeting request email to $recipientEmail";
		    $this->logResult('Failure', $logMessage);
	    }   
	    
    }
    
   
    
    public function getTemplateNameByAction($action) {
	   
	    switch ($action) {
//		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY:
//			   $this-> subjectTemplateName = "Assigned a Vacancy";
//			   $this-> bodyTemplateName = "";
//		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST:
			    $this-> subjectTemplateName = "candidateShortlistSubject.txt";
			    $this-> bodyTemplateName = "candidateShortlistBody.txt";
		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT:
			    $this-> subjectTemplateName = "rejectCandidateSubject.txt";
			    $this-> bodyTemplateName = "rejectCandidateBody.txt";
		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW:
			    $this-> subjectTemplateName = "meetingRequestSubject.txt";
			    $this-> bodyTemplateName = "scheduleInterviewBody.txt";
		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED:
			    $this-> subjectTemplateName = "interviewPassSubject.txt";
			    $this-> bodyTemplateName = "interviewPassBody.txt";
		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED:
			    $this-> subjectTemplateName = "interviewFailSubject.txt";
			    $this-> bodyTemplateName = "interviewFailBody.txt";
		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_OFFER_JOB:
			    $this-> subjectTemplateName = "offerCandidateSubject.txt";
			    $this-> bodyTemplateName = "offerCandidateBody.txt";
		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_DECLINE_OFFER:
			    $this-> subjectTemplateName = "rejectCandidateSubject.txt";
			    $this-> bodyTemplateName = "rejectCandidateBody.txt";
		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE:
			    $this-> subjectTemplateName = "offerCandidateSubject.txt";
			    $this-> bodyTemplateName = "offerCandidateBody.txt";
		    break;
		    case PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW:
				$this-> subjectTemplateName = "scheduleInterviewSubject.txt";
			    $this-> bodyTemplateName = "scheduleInterviewBody.txt";
		    break;
	    }
	    
    }
 
}
