@php

use App\Enums\LifeCycleStagesEnum;
use Illuminate\Support\Str;

@endphp

<div class="modal fade" id="editContactModal" tabindex="-1" role="dialog" aria-labelledby="editContactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="editContactModalLabel">Update contact</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="contact-edit-form" data-action="{{ route('contacts.store') }}" method="POST" id="contact-edit-form">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" name="firstname" id="firstnameEdit" aria-describedby="firstnameHelpEdit" placeholder="Enter first name" value="hello">
                            <small id="firstnameHelpEdit" class="form-text text-danger d-none">First name is required</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" name="lastname" id="lastnameEdit" aria-describedby="lastnameHelpEdit" placeholder="Enter last name">
                            <small id="lastnameHelpEdit" class="form-text text-danger d-none">Last name is required</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" name="email" id="emailEdit" aria-describedby="emailHelpEdit" placeholder="Enter email">
                            <small id="emailHelpEdit" class="form-text text-danger d-none">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" name="phone" id="phoneEdit" aria-describedby="phoneHelpEdit" placeholder="Enter phone number">
                            <small id="phoneHelpEdit" class="form-text text-danger d-none">Phone number is required</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="job_title">Job Title</label>
                        <input type="text" class="form-control" name="jobtitle" id="jobtitleEdit" aria-describedby="job_titleHelpEdit" placeholder="Enter job title">
                        <small id="jobtitleHelpEdit" class="form-text text-danger d-none">Job title is required</small>
                    </div>
                    <div class="form-group">
                        <label for="lifecycle_stage">Lifecycle Stage</label>
                        <select class="form-control" name="lifecyclestage" id="lifecyclestageEdit" aria-describedby="lifecycle_stageHelpEdit">
                            <option value="">Select lifecycle stage</option>
                            @foreach (LifeCycleStagesEnum::cases() as $stage)
                            <option value="{{ $stage->value }}">{{ Str::of($stage->name)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                        <small id="lifecyclestageHelpEdit" class="form-text text-danger d-none">Lifecycle stage is required</small>
                    </div>
                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="text" class="form-control" name="website" id="websiteEdit" aria-describedby="websiteHelpEdit" placeholder="Enter website">
                        <small id="websiteHelpEdit" class="form-text text-danger d-none">Website is required</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit-contact-submit-btn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>