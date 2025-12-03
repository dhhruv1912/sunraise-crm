<div class="row">
    <div class="col-12">

        <label class="form-label mb-2 fw-bold">Project Notes</label>

        {{-- Quill Editor Wrapper --}}
        <div id="project_note_editor"
             style="height: 280px;"
             class="border rounded bg-white">
            {!! $project->project_note !!}
        </div>

        {{-- Hidden input that will store final HTML --}}
        <input type="hidden" name="project_note" id="project_note">
    </div>
</div>
