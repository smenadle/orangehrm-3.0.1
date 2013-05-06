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
                                            'vacancyName' => $this->replacements['vacancyName']
                                            );
            }else{
            	$this->bodyReplacements = array('recipientFirstName' => $this->replacements['recipientFirstName'],
                                            'candidateName' => $this->replacements['candidateName'],
                                            'vacancyName' => $this->replacements['vacancyName'],
                                            'interviewer' => $this->replacements['interviewer'],
                                            'interviewType' => $this->replacements['interviewType'],
                                            'interviewDate' => $this->replacements['interviewDate'],
                                            'interviewTime' => $this->replacements['interviewTime'],
                                            'note' => $this->replacements['note'],
                                            );
            	
            }

        }

        return $this->bodyReplacements;
        
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
			    $this-> subjectTemplateName = "scheduleInterviewSubject.txt";
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
