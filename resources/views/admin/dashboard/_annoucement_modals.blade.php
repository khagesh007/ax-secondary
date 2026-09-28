    <!-- Default Modals -->
    <div id="annoucementModel" class="modal fade" tabindex="-1" aria-labelledby="annoucementModel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog  modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="annoucementModelTitle">📣 Acknowledgement Alert</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button> --}}
                </div>

                <div class="modal-body">


                    <p>Please note the following rules will be effective from <b>Tuesday, 8th April 2025</b>:</p>
                   <br>

                    <p>
                    If a lead is not actioned within <b>24 hours</b>, it will be moved to the <b>Portal Lead Pool</b>, where it becomes available to all agents. Once moved, you will not be able to claim the lead back.
                    </p>
                    <p>
                    If a lead remains in <b>"In Progress"</b> status for more than <b>72 hours</b>, it will also be moved to the <b>Portal Lead Pool</b> and become available to all agents. Again, you will not be able to take the lead back.
                    </p>
                    <p>
                    It is your responsibility to keep the CRM updated. Failure to do so will result in the loss of leads.
                    </p>

                    <div class="row mt-2">
                        <div class=" col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acknowledged">
                                <label class="form-check-label" for="acknowledged">
                                    <b>I have read and acknowledge this message.</b>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>







                <div class="modal-footer border-0 justify-content-end">
                    {{-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button> --}}
                    <button type="button" class="btn btn-primary" id="acknowledge-btn">Yes, I Acknowledge</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <?php /*
    <div id="annoucementModelold" class="modal fade" tabindex="-1" aria-labelledby="annoucementModelold" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog  modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="annoucementModelold">📣 Announcement</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button> --}}
                </div>

                <div class="modal-body">
                    <p>Dear Colleagues, <br>❗️Sign up for this week's Trainings❗️
                    </p>

                    <span>
                        <p><b>💰''Financial Planning Workshop''</b></p>
                        <p> Learn how to manage fluctuating incomes effectively and create budgeting strategies.
                            By a Certified Financial Coach
                        </p>

                        <p><b> Monday, 10:30 - 12:00 <br>Concord, Pantry Area <br> With Certified Financial Coach</b></p>
                    </span>
                    <hr>
                    <span>
                        <p><b>✨''Mindfulness''</b></p>
                        <p><b> Tuesday, 14:00 - 14:30 <br>Westburry, Cinema Room </b></p>
                    </span>
                    <hr>

                    <span>
                        <p><b>🎯''Goal Setting - The First Step to achieve your Goals''</b></p>
                        <p> Join to gain practical tools for defining your yearly goals and crafting an actionable plan during the session.
                        </p>
                        <p><b> Wednesday, 10:00 -12:00<br>Westburry, 8th floor<br>By Anastasia </b></p>
                    </span>
                    <hr>
                    <span>
                        <p><b>✨''Mindfulness''</b></p>
                        <p><b> Wednesday, 14:00 - 14:30 <br>Westburry, Cinema Room </b></p>
                    </span>
                    <hr>

                    <span>
                        <p><b>📞''Calling Structure''</b></p>
                        <p> Join for an engaging workshop designed to help you make more effective and confident calls when reaching out to Primary market leads.
                        </p>
                        <p><b> Thursday, 10:00 -12:30<br>Westburry, 8th floor <br>By Brian </b></p>
                    </span>
                    <hr>

                    <span>
                        <p><b>☺️''Emotional Intelligence in Sales''</b></p>
                        <p>Understand and respond to customers' emotions to ultimately drive sales through emotional insight.
                        </p>
                        <p><b> Thursday, 10:00 -12:00<br>Concord, Boardroom, <br>By Anastasia</b></p>
                    </span>
                    <hr>
                    <h6>
                        HOW TO SIGN UP?

                    </h6>
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="text-muted mb-0">Go to the <a href="https://axcapital.talentlms.com/dashboard"
                                    target="_blank">AX ACADEMY</a> in the CRM</p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="text-muted mb-0">Click on the "Desired Session" in the "Live Sessions" category

                            </p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="text-muted mb-0">Register for the training

                            </p>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class=" col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acknowledged">
                                <label class="form-check-label" for="acknowledged">
                                    Do not show this message again.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>







                <div class="modal-footer border-0 justify-content-end">
                    {{-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button> --}}
                    <button type="button" class="btn btn-primary" id="acknowledge-btn">Yes, I Acknowledge</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
*/

/*
    <div id="format_annoucementModel" class="modal  fade" tabindex="-1" aria-labelledby="format_annoucementModel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog  modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="format_annoucementModel">📣 Announcement</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button> --}}
                </div>

                <div class="modal-body">
                    {{-- <p>Dear Colleagues, <br>Sign up now for this Training on Wednesday.</p> --}}
                    <h4 class="mb-3 text-center">Attendance and Timekeeping Policy</h4>

                    <hr>
                    <p>
                    <ul class="list-unstyled">
                        <li>
                            <b>Department</b>: Human Resources
                        </li>
                        <li>
                            <b>Effective Date</b>: January 30, 2025
                        </li>
                        <li>
                            <b>Last Updated</b>: January 30, 2025
                        </li>
                    </ul>



                    </p>
                    <p>
                        <b>Purpose</b> <br>
                        The purpose of this <b>Attendance and Timekeeping Policy</b> is to establish clear
                        expectations regarding employee attendance, punctuality, and timekeeping for both
                        back-office employees and sales agents. This ensures accurate tracking of working hours,
                        compliance with labour regulations, and maintenance of overall productivity.
                    </p>
                    <hr>
                    <p>
                        <b>Scope</b> <br>
                        This policy applies to all employees, including Sales Agents and Back-Office staff, and
                        governs timekeeping practices, attendance expectations, and reporting procedures.
                    </p>
                    <hr>
                    <p>
                        <b>Policy Statement</b> <br>
                        AX CAPITAL is committed to maintaining a disciplined and transparent timekeeping
                        system that upholds company standards and aligns with labour regulations. This policy
                        outlines responsibilities, procedures, and disciplinary measures to ensure adherence to
                        attendance and punctuality requirements.
                    </p>
                    <hr>
                    <b>
                        <h4>Timekeeping and Attendance Guidelines</h4>
                    </b>
                    <h4>I. Back office Employees</h4>
                    <p>
                        <b>a. Office Hours:</b><br>
                    <ol type="i" class="ms-3">
                        <li>Monday to Friday: 9:30 AM to 6:30 PM</li>
                        <li>Saturday: 11:00 AM to 3:00 PM</li>
                    </ol>
                    </p>

                    <p>
                        <b>b. Total Work Hours:</b><br>
                    <ol type="i" class="ms-3">
                        <li>Each workday consists of 9 hours, including a 1-hour break.</li>
                    </ol>
                    </p>
                    <b>c. Workday Schedule:</b><br>
                    <p>
                    <ul>
                        <li>
                            <b>Monday to Friday:</b>
                        </li>


                        <ol type="i" class="ms-3">
                            <li>Start Time: 9:30 AM</li>
                            <li>End Time: 6:30 PM</li>
                            <li>Break: 1 hour, fexible between 12:30 PM to 2:30 PM</li>
                        </ol>


                        <li>
                            <b>Saturday:</b>
                        </li>
                        <ol type="i" class="ms-3">
                            <li>Start Time: 11:00 AM</li>
                            <li>End Time: 3:00 PM</li>
                            <li>No scheduled break</li>
                        </ol>
                    </ul>
                    </p>


                    <p>
                        <b>d. Attendance Tracking:</b><br>
                    <ol type="i" class="ms-3">
                        <li>Employees must clock in and out daily using the Bitrix24 application.</li>
                        <li>Accurate records of working hours, including break times, must be logged in Bitrix24.</li>
                        <li>Failure to clock in or out will result in being marked absent for the day.</li>
                    </ol>
                    </p>


                    <p>
                        <b>e. Attendance Expectations:</b><br>
                    <ol type="i" class="ms-3">
                        <li>Employees must arrive and clock in no later than 9:30 AM each workday.</li>
                        <li>Clocking out should occur after completing the required work hours.</li>
                    </ol>
                    </p>


                    <p>
                        <b>f. Breaks:</b><br>
                    <ol type="i" class="ms-3">
                        <li>Employees are entitled to a 1-hour break (Monday to Friday) between 12:30 PM and 2:30 PM.
                        </li>
                        <li>No scheduled break on Saturdays, but short breaks are permitted as needed.</li>
                    </ol>
                    </p>
                    <p>
                        <b>g. Alternative Clock-In Locations:</b><br>
                    <ol type="i" class="ms-3">
                        <li>For Back Office employees, only AX CAPITAL offices are approved locations for clocking in
                            and out.</li>
                    </ol>
                    </p>
                    <p>
                        <b>h. Deductions for Absence and Late Arrivals</b><br>
                    <ol type="i" class="ms-3">
                        <li>Absences without prior approval will result in salary deductions or leave balance
                            adjustments.</li>
                        <li>Late arrivals (after 9:30 AM) without valid reasons or approval will be subject to salary
                            deductions or disciplinary actions.</li>
                    </ol>
                    </p>
                    <p>
                        <b>i. Disciplinary Actions for Non-Compliance:</b><br>
                    <ol type="i" class="ms-3">
                        <li>First Violation: Verbal Warning</li>
                        <li>Second Violation: First Written Warning</li>
                        <li>Third Violation: Second Written Warning</li>
                        <li>Fourth Violation: Final Written Warning, which may lead to further disciplinary action,
                            including termination.</li>
                    </ol>
                    </p>
                    <p>
                        <b>j. Overtime and Flexibility:</b><br>
                    <ol type="i" class="ms-3">
                        <li>Overtime may be required based on business needs. Prior notice will be given when possible.
                        </li>
                        <li>Flexibility in work hours may be considered with approval from the manager and HR.</li>
                    </ol>
                    </p>

                    <p>
                        <b>k. Remote Work Requests</b><br>
                    <ol type="i" class="ms-3">
                        <li>Employees requesting remote work or work from home must submit a formal request in the CRM,
                            detailing the reasons. The request must be approved by their manager in the system, subject
                            to business needs and team dynamics.</li>
                        <li>Remote work arrangements or exceptions to standard office hours may be permitted under
                            special circumstances with prior approval from management and HR.</li>
                    </ol>
                    </p>
                    <p>
                        <b>l. Communication</b><br>
                    <ol type="i" class="ms-3">
                        <li>Employees are encouraged to discuss any attendance-related concerns with their supervisor or
                            HR in an open and transparent manner.</li>
                    </ol>
                    </p>
                    <p>
                        <b>m. Leave Submission for Payroll:</b><br>
                    <ol type="i" class="ms-3">
                        <li>All leave requests submitted in the CRM must be approved by line managers before the 15th of
                            each month to ensure they are included in the payroll process.</li>
                    </ol>
                    </p>
                    <p>
                        <b>n. Absence Procedure</b><br>
                    <ol type="i" class="ms-3">
                        <li>Line managers should be informed in advance of any planned absences. Any leave should be
                            applied in the CRM and approved by the manager. For sick leave longer than 2 days, a
                            DHA-approved sick leave certificate must be provided and attached to the CRM application.
                        </li>
                    </ol>
                    </p>

                    <p>
                        <b>o. Technical Difficulties for Clocking In/Out</b><br>
                    <ol type="i" class="ms-3">
                        <li>If an employee encounters technical difficulties while clocking in or out, they must
                            immediately contact <b>Maria Castrillo</b> from HR Department with a screenshot of the
                            issue, or inform her if they forgot to log in.</li>
                    </ol>
                    </p>

                    <h4>II. Sales Agents</h4>
                    <p>
                        <b>a. Attendance Tracking:</b><br>
                    <ol type="i" class="ms-3">
                        <li>All sales agents must use the Bitrix24 application to clock in and clock out daily. This
                            system will monitor monthly attendance in compliance with labour regulations.</li>
                    </ol>
                    </p>
                    <p>
                        <b>b. Punctuality</b><br>
                    <ol type="i" class="ms-3">
                        <li>Sales agents must arrive at the office by 9:30 AM if they do not have any meetings or
                            viewings with clients. Punctuality is also expected during team meetings with their line
                            managers. Arriving more than 15 minutes late will be considered an absence and may result in
                            disciplinary action.</li>
                    </ol>
                    </p>
                    <p>
                        <b>c. Alternative Clock-In Locations</b><br>
                    <ol type="i" class="ms-3">
                        <li>For sales agents, clock-ins are valid at different locations such as AX CAPITAL offices,
                            developer offices, or any place where you have meetings or viewings, as long as the location
                            matches the details you've submitted to your manager for that day.</li>
                    </ol>
                    </p>
                    <p>
                        <b>d. Disciplinary Actions for Non-Compliance</b><br>
                    <ol type="i" class="ms-3">
                        <li>First Violation: Verbal Warning</li>
                        <li>Second Violation: First Written Warning</li>
                        <li>Third Violation: Second Written Warning</li>
                        <li>Fourth Violation: Final Written Warning, which may lead to further disciplinary action,
                            including termination.</li>
                    </ol>
                    </p>
                    <h4>III. Executive Assistants</h4>
                    <p>
                        <b>a. Attendance Tracking</b><br>
                    <ol type="i" class="ms-3">
                        <li>Executive Assistants must use the Bitrix24 application to clock in and clock out daily.
                            However, due to the nature of their role, they are permitted to clock in and out from any
                            location, as their responsibilities may require flexibility in terms of location and working
                            hours.</li>
                    </ol>
                    </p>
                    <p>
                        <b>b. Punctuality and Availability</b><br>
                    <ol type="i" class="ms-3">
                        <li>While Executive Assistants may work outside of the regular office hours due to the demands
                            of their role, this does not qualify for overtime pay unless specifically approved in
                            advance by management. Working beyond the standard hours is understood to be part of their
                            responsibilities and does not entitle them to additional compensation unless prior agreement
                            is made.</li>
                    </ol>
                    </p>
                    <p>
                        <b>d. Alternative Clock-In Locations</b><br>
                    <ol type="i" class="ms-3">
                        <li>Executive Assistants are allowed to clock in and out from any location, including but not
                            limited to AX CAPITAL offices, external meetings, or while working remotely, as long as it
                            aligns with the tasks and duties assigned by their supervisor. This must be approved by
                            their supervisor in advance to ensure proper documentation and compliance with company
                            policies.</li>
                    </ol>
                    </p>
                    <hr>
                    <h4>Compliance</h4>
                    <p>
                        Failure to comply with this policy may result in disciplinary actions, including but not
                        limited to verbal and written warnings, salary deductions, or termination, as deemed
                        necessary by the HR Department. All employees and sales agents are expected to adhere
                        strictly to the attendance and timekeeping guidelines to maintain a productive and
                        efficient work environment. <br><br>
                        HR will monitor attendance records and address any violations accordingly. Line
                        managers must ensure their teams comply with the policy and report any persistent
                        violations to HR.
                    </p>
                    <h4>Policy Review</h4>
                    <p>
                        This policy will be reviewed periodically to ensure it remains relevant and aligned with
                        company objectives and labour regulations. Any updates or revisions will be
                        communicated to all employees in a timely manner. <br><br>
                        All employees are required to acknowledge and adhere to the updated policy, and any
                        changes will take effect as per the HR Department's notice.
                    </p>
                    <p>
                        <b>Acknowledgment:</b><br>
                        By clicking <b>'Yes, I Acknowledge'</b> below, you confirm that you have read, understood, and
                        agreed to comply with the attendance and timekeeping policies as
                        stated.
                    </p>

                    <hr>


                    <div class="row mt-2">
                        <div class=" col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acknowledged">
                                <label class="form-check-label" for="acknowledged">
                                    Do not show this message again.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>







                <div class="modal-footer border-0 justify-content-end">
                    {{-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button> --}}
                    <button type="button" class="btn btn-primary" id="acknowledge-btn">Yes, I Acknowledge</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
*/
?>
