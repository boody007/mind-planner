$(function () {
    // Setting mission creator modal values by the clicked button
    document.querySelector('.mission-creator').addEventListener('click', function (e) {
        var target = this.getAttribute('data-bs-target');
        var modalTitle = document.querySelector(target + " .modal-header strong");
        var creatorContent = this.getAttribute('data-content');
        modalTitle.innerHTML = creatorContent;
    });
    // Controlling the campaign creator modal input length
    $('.limited-box input').keyup(function (e) { 
        var maxLength = $(this).attr('maxlength');
        var currentLength = $(this).val().length;
        $(this).next().find('strong span').text(currentLength);
        if (currentLength >= maxLength) {
            e.preventDefault();
            $(this).next().find('strong').addClass("text-danger");
            $(this).next().find('strong span').addClass("text-danger");
            $(this).next().find('strong:first-child').text("Max length reached!");
        }
        else {
            $(this).next().find('strong').removeClass("text-danger");
            $(this).next().find('strong span').removeClass("text-danger");
            $(this).next().find('strong:first-child').text("");
        }
    });    
    // Setting objective details modal data with the objective card clicked
    $('.objective[data-bs-toggle="modal"]').click(function (e) { 
        e.preventDefault();
        var objectiveTitle = $(this).attr('data-title');
        var objectiveContent = $(this).attr('data-content');
        var objectiveImg = $(this).attr('data-img');
        var objectiveDateTime = $(this).attr('data-date-time');
        var objectiveID = $(this).attr('data-id');
        // Arranging data in the modal
        $('#objectiveShow .modal-header .modal-title').text(objectiveTitle);
        $('#objectiveShow .modal-body .image-view img').attr("src", objectiveImg);
        $('#objectiveShow .modal-body p.content').text(objectiveContent);
        $('#objectiveShow .modal-body p.date-time strong').text(objectiveDateTime);
        $('#objectiveShow').attr("data-obj-id", objectiveID);
        $('#objectiveShow .step-creator').attr("data-obj-id", objectiveID);
        $('#objectiveShow .step-creator').attr("data-obj-name", objectiveTitle);
    });
    // Setting step creator modal
    $('.step-creator').click(function (e) { 
        // Removing show class of the previous modal
        $('#objectiveShow').modal("hide");
        var objectiveName = $(this).data('obj-name');
        var objectiveID = $(this).data('obj-id');
        targetModal = $(this).data('modal-target');
        // Arranging data in the modal
        $(`#stepCreation .modal-header .modal-title`).html(`New step in ${objectiveName} objective`);
        $(`#stepCreation form .modal-body input[name="target_objective"]`).val(objectiveID);
        // Toggling targeted modal
        $('#stepCreation').modal("show");
        $('#stepCreation').addClass("show");
    });
    // Limiting inputs
    function limited (e) {
        if (this.value.length >= 20) {
            e.preventDefault();
            this.nextElementSibling.innerHTML = "Max length reached!";
            this.nextElementSibling.classList.add("text-danger");
        }
    }
    // Listing objective's steps in the modal by its id
    $('.card.objective').click(function (e) {
        const stepsView = document.querySelector(".modal#objectiveShow .modal-body .steps");
        var objectiveID = $(this).data('id');
        if ($('#objectiveShow').data("id") != objectiveID) {
            stepsView.querySelector('ul').innerHTML = "";
            // Requesting script
            $.post("assets/php/handler.php", {objective_id: objectiveID, action: "listSteps"},
                function (data, textStatus, jqXHR) {
                    var steps = JSON.parse(data);
                    if (Array.isArray(steps)) {
                        // Removing the Alert tip if exists
                        if (stepsView.contains(stepsView.querySelector(".text-muted"))) {
                            stepsView.querySelector(".text-muted").remove();
                        }
                        // Looping on objects
                        for (var step of steps) {
                            stepsView.querySelector('ul').innerHTML += `<div class='step-box'>
                                <a ${(step.link != null && step.link != "") ? "href='" + step.link + "' " +  "target='_blank'" : ""} class='context-trigger' data-target-type='step'><li ${(step.image != null) ? `class='image-container' style='background-image:url(${step.image}) !important'` : ""}><div class="mask"></div><strong>${step.title}</strong><br><br><p class="lead ${(step.image != null) ? "mb-3" : ""}">${step.content}</p></li></a>
                                <div class='step-actions' data-step-id='${step.id}' data-step-title='${step.title}'>
                                    <div class='edit' id='step-edit'>Edit</div>
                                    <div class='delete' id='step-delete'>Delete</div>
                                </div>
                            </div>`;
                        }
                    }
                    else {
                        // Check for Alert tip existence before creating it
                        if (!stepsView.contains(stepsView.querySelector(".text-muted"))) {
                            const alerttip = document.createElement("div");
                            alerttip.classList.add("text-muted");
                            alerttip.innerHTML = "Nothing for now...";
                            document.querySelector("#objectiveShow .modal-body .steps").appendChild(alerttip);    
                        }
                    }
                },
            );
            // Setting the id of the current opened objective
            $('#objectiveShow').data("id", objectiveID);
        }
    });
    // Showing data of an uploaded image file in objective creator
    document.getElementById("image-objective").addEventListener("change", () => {
        if (this.files.length > 0) {
            this.nextElementSibling.innerHTML = this.files[0].name;
        }
    });
    // Controlling the Modals content with the clicked buttons
    document.querySelector('.mission-creator').addEventListener('click', function (e) {
        var target = this.getAttribute('data-bs-target');
        var modalTitle = document.querySelector(target + " .modal-header strong");
        var creatorContent = this.getAttribute('data-content');
        var creatorID = this.getAttribute('data-campaign-id');
        modalTitle.innerHTML = creatorContent;
        $(`${target} form input#target-campaign`).val(creatorID);
    });
    // Showing the image of the link in the objective creator
    $('#image-objective').keyup(function (e) { 
        var link = $(this).val();
        if (link != "") {
            $(this).next().attr("src", link);
        }
        else {
            $(this).next().attr("src", "");
        }
    });
    // Checking for new objective added from the querystring
    var urlParams = new URLSearchParams(window.location.search);
    var newObjective = urlParams.get('new_added');
    // Selecting all cards
    var objectives = document.querySelectorAll('.card.objective');
    // Looping on cards
    for (var objective of objectives) {
        // Checking for the new added objective
        if (objective.getAttribute("data-id") == newObjective) {
            // Adding a class to the new added objective card
            objective.classList.add("newest-add");
        }
    }
    // Disabling new added objective card animation after a while of adding it
    setTimeout(() => {
        $('.card.objective.newest-add').addClass("hidden");
        $('.card.objective.hidden').removeClass("newest-add");
    }, 7500);
    setTimeout(() => {
        $('.card.objective').removeClass("hidden");
    }, 8000);
    // Showing source icon properties view in mission creator form when choosing source icon
    $('#icon-type').change(function (e) { 
        e.preventDefault();
        if ($(this).val() == 3) {
            $('.source-icon-properties').css("display", "block");
            $('.source-icon-properties #icon-content-type').css("display", "block");
            $('#icon-mission').attr("placeholder", "Icon Content");
        }
        else if ($(this).val() == 2) {
            $('.source-icon-properties').css("display", "block");
            $('.source-icon-properties #icon-content-type').css("display", "none");
            $('#icon-mission').attr("placeholder", "FontIcon Class");
        }
        else {
            $('.source-icon-properties').css("display", "none");
            $('#icon-mission').attr("placeholder", "Icon Code");
        }
    });
    // Setting the source icon content input's placeholder by the selected content type
    $('#icon-content-type').change(function (e) { 
        e.preventDefault();
        var content = $(this).val();
        var placeholder = "";
        switch (content) {
            case "1":
                placeholder = "Icon Text";
                break;
            case "2":
                placeholder = "Icon Image Link";
                break;
            default:
                alert("Invalid content type!");
                break;
        }
        $('#icon-mission').attr("placeholder", placeholder);
    });
    // Controlling showed chars count from the objective's content
    var objectivesContent = document.querySelectorAll(".objective");
    objectivesContent.forEach((objective) => {
        const charsLimit = objective.querySelector(".card-text").getAttribute("data-max-char");
        var objectiveContent = objective.querySelector(".card-text").innerHTML;
        // Cutting off the content string when chars limit exceeded
        if (objectiveContent.length > charsLimit) {
            objective.querySelector(".card-text").innerHTML = objectiveContent.substring(0, charsLimit) + "...";
        }
    });
    // Hovering Background for the objective card's actions
    $(".content .objectives .card.objective .card-actions div").hover(
        function () {
            // On mouse enter
            const parentBgColor = $(this).attr("hover-color");
            // Convert RGB color to a darker shade
            const darkerColor = darkenColor(parentBgColor, 0.8);
            // Apply the darker color to the child
            $(this).css("background-color", darkerColor);
        },
        function () {
          // On mouse leave, reset child's background color
            $(this).css("background-color", "");
        }
    );
    
    // Helper function to darken an RGB color
    function darkenColor(rgb, factor) {
        const rgbValues = rgb.match(/\d+/g).map(Number);
        const darken = (channel) => Math.max(0, Math.floor(channel * factor));
        return `rgb(${darken(rgbValues[0])}, ${darken(rgbValues[1])}, ${darken(rgbValues[2])})`;
    }
    // Context Menu Trigger
    var $doc = $( document ),
    $context = $(".context"),
    $triggers = $(".context-trigger"); // Add the trigger element

    $triggers.each(function() { // Use each() to iterate over multiple triggers
        $(this).on("contextmenu", function(e) { // Change event to contextmenu on each trigger            
            e.preventDefault();
            $('.context').addClass("is-visible");
            $('.context').css("top", e.pageY + "px");
            $('.context').css("left", e.pageX + "px");
            // Check the context trigger target type
            var targetType = $(this).attr("data-target-type");
            $context.find(".context__header span").text(targetType);
            // Copying the target id
            $context.attr("data-target-id", $(this).attr("data-id"));
            $context.attr("data-target-type", targetType);
            if (this.hasAttribute('data-title')) {
                $context.attr("data-target-name", $(this).attr("data-title"));
            }           
        });
    });
    
    $doc.on("click", function(event) {
        if (!event.target.closest(".context") && !event.target.closest(".context-trigger")) {
            $context.removeClass("is-visible");
        }
    });
    $context.on("mousedown touchstart", ".context__item:not(.context__item--nope)", function(e) {
        
        if( e.which === 1 ) {

            var $item = $(this);

            $item.removeClass("context__item--active");

            setTimeout( function() {
                $item.addClass("context__item--active");
            },10);
            
        }
        
        $context.removeClass("is-visible");
    });
    // Dismissing context menu using escape keyboard key
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $context.removeClass("is-visible");
        }
    });
    // Context menu actions
    $(".context__item").click(function (e) { 
        var action = $(this).attr("data-action");
        var target = $(this).parent().attr("data-target-id");
        var targetType = $(this).parent().attr("data-target-type");
        var targetName = $(this).parent().attr("data-target-name");
        switch (action) {
            case "edit":
                // Creating keyup event
                var keyup = new KeyboardEvent("keyup", {key: "Control", code: "ControlLeft", bubbles: true});
                switch (targetType) {
                    case "objective":
                            // Retrieving the objective data
                            var objectiveTitle = $(`.card.objective[data-id='${target}']`).attr("data-title");
                            var objectiveContent = $(`.card.objective[data-id='${target}']`).attr("data-content");
                            var objectiveImgLink = $(`.card.objective[data-id='${target}']`).attr("data-img");
                            var objectivePriority = $(`.card.objective[data-id='${target}']`).attr("data-priority");
                            var objectiveMission = $(`.card.objective[data-id='${target}']`).parent().attr("data-id");
                            // Triggering the objective creator modal with the objective data
                            $('#objectiveCreator').modal("show");
                            $('#objectiveCreator form input#create-objective').val("Edit");
                            $('#objectiveCreator form input#create-objective').attr("name", "edit_objective");
                            $('#objectiveCreator .modal-header .modal-title').text(`Edit ${objectiveTitle} Objective`);
                            $('#objectiveCreator form input#name-objective').val(objectiveTitle);
                            document.querySelector('#objectiveCreator form input#name-objective').dispatchEvent(keyup);
                            $('#objectiveCreator form textarea#content-objective').val(objectiveContent);
                            $('#objectiveCreator form select#priority-objective').val(objectivePriority);
                            $('#objectiveCreator form select#mission-objective').val(objectiveMission);
                            $('#objectiveCreator form input#image-objective').val(objectiveImgLink);
                            $('#objectiveCreator form .image-link-preview').attr("src", objectiveImgLink);
                            // Setting the modal with the objective id
                            $('#objectiveCreator form input#objective-id').val(target);
                    break;
                    case "campaign":
                        
                    break;

                }
                break;
            case "delete":
                switch (targetType) {
                    case "objective":
                        // Deleting the objective
                        if (confirm(`Are you sure you want to delete ${targetName} objective?`)) {
                            $.post("assets/php/handler.php", {action: "delete_objective", objective_id: target}, function (data, textStatus, jqXHR) {
                                // Decoding JSON
                                var response = JSON.parse(data);
                                // Checking for status
                                if (response.status == "success") {
                                    // Removing the objective card
                                    $(`.card.objective[data-id='${target}']`).remove();
                                    // Showing success message alert
                                    var alert = document.querySelector(".alert");
                                    if (document.contains(alert)) {
                                        alert.classList.remove("hidden");
                                        alert.classList.remove("alert-danger");
                                        alert.classList.remove("alert-warning");
                                        alert.classList.remove("alert-info");
                                        alert.classList.remove("alert-primary");
                                        alert.classList.remove("alert-success");
                                        alert.classList.add("alert-success");
                                        alert.querySelector("strong").innerHTML = `Objective ${targetName} deleted successfully!`;
                                        setTimeout(() => {
                                            alert.classList.add("hidden");
                                            alert.classList.remove("alert-success");
                                            alert.querySelector("strong").innerHTML = "";
                                        }, 3000);
                                    }
                                    else {
                                        alert = document.createElement("div");
                                        alert.classList.add("alert", "alert-success", "hidden");
                                        alert.innerHTML = `<strong>Objective ${targetName} deleted successfully!</strong>`;
                                        document.body.appendChild(alert);
                                        setTimeout(() => {
                                            alert.classList.add("hidden");
                                            alert.classList.remove("alert-success");
                                            alert.querySelector("strong").innerHTML = "";
                                        }, 3000);
                                    }
                                }
                                else {
                                    // Showing error message
                                    alert("Something went wrong!");
                                }
                            });
                        }
                    break;
                }
            break;
            default:
                break;
        }
    });
    // Showing step actions box on right click the step box
    $('.modal#objectiveShow').on("shown.bs.modal", function () {
        const steps = document.querySelectorAll(".step-box");
        steps.forEach((step) => {
            step.addEventListener("contextmenu", (e) => {
                e.preventDefault();
                const stepActions = step.querySelector(".step-actions");
                stepActions.classList.toggle("showed");
            });
        });    
    });
    // Deleting steps
    const stepsBoxes = document.querySelectorAll(".steps .step-box");
    stepsBoxes.forEach((stepBox) => {
        const stepDelete = stepBox.querySelector(".step-actions #step-delete");
        $(stepDelete).click(function (e) { 
            e.preventDefault();
            var stepID = stepBox.getAttribute("data-step-id");
            var stepTitle = stepBox.getAttribute("data-step-title");
            // Confirming the deletion
            if (confirm(`Are you sure deleting the ${stepTitle} step?`)) {
                $.post("assets/php/handler.php", {action: "delete_step", step_id: stepID}, function (data, textStatus, jqXHR) {
                    // Decoding JSON
                    var response = JSON.parse(data);
                    // Checking for status
                    if (response.status == "success") {
                        // Removing the step box
                        stepBox.remove();
                        // Showing success message alert
                        var alert = document.querySelector(".alert");
                        if (document.contains(alert)) {
                            alert.classList.remove("hidden");
                            alert.classList.remove("alert-danger");
                            alert.classList.remove("alert-warning");
                            alert.classList.remove("alert-info");
                            alert.classList.remove("alert-primary");
                            alert.classList.add("alert-success");
                            alert.querySelector("strong").innerHTML = `Step ${stepTitle} deleted successfully!`;
                            setTimeout(() => {
                                alert.classList.add("hidden");
                                alert.classList.remove("alert-success");
                                alert.querySelector("strong").innerHTML = "";
                            }, 3000);
                        }
                        else {
                            alert = document.createElement("div");
                            alert.classList.add("alert", "alert-success", "hidden");
                            alert.innerHTML = `<strong>Step ${stepTitle} deleted successfully!</strong>`;
                            document.body.appendChild(alert);
                            setTimeout(() => {
                                alert.classList.add("hidden");
                                alert.classList.remove("alert-success");
                                alert.querySelector("strong").innerHTML = "";
                            }, 3000);
                        }
                    }
                    else {
                        // Showing error message
                        alert("Something went wrong!");
                    }
                });
            }
        });
    });
});