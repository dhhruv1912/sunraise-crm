<div class="">
    <div id="projectNoteWrapper">
        <textarea name="note" id="project_note_hidden" class="d-none">{{ $project->project_note }}</textarea>
        <div class="h-px-250" id="project_note">{!! @$project->project_note ?? '' !!}
        </div>
    </div>

    <div class="col-12 text-end mt-2">
        <button class="btn btn-sm btn-outline-primary" id="saveProjectNote">Save Note</button>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const project_note_quill = new Quill('#project_note', {
            theme: 'snow'
        });
        document.addEventListener('click',async function (e) {
            if (e.target.matches('#saveProjectNote')) {
                project_note = document.getElementById("project_note_hidden").value
                const data = {
                    project_note,
                    'ajax' : true
                };
                console.log(`/projects/${PROJECT_ID}/update`);
                // return
                const res = await crmFetch(`/projects/${PROJECT_ID}/update`, {
                        method: "POST",
                        body: JSON.stringify(data),
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const json = await res.json();

                    if (!json.status) {
                        alert(json.message);
                        return;
                    }

                    alert("Project note updated successfully!");
                
            }
        })
    });
    document.addEventListener('keyup', function (e) {
        if (e.target.matches('#projectNoteWrapper .ql-editor')) {
            if (e.target.classList.contains('ql-blank')) {
                document.getElementById("project_note_hidden").value = ""
            } else {
                document.getElementById("project_note_hidden").value = e.target.innerHTML
            }
        }
    })
</script>