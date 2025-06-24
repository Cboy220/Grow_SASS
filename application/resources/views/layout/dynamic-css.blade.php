<!-- This file must NOT be formatted -->
<style>
    :root {
        --calendar-type-event-background: {{ config('system.settings2_calendar_events_colour') }};

        --calendar-type-project-background: {{ config('system.settings2_calendar_projects_colour') }};

        --calendar-type-task-background: {{ config('system.settings2_calendar_tasks_colour') }};

        --calendar-fc-daygrid-dot-event-background: {{ config('system.settings2_calendar_events_colour') }};

        --calendar-fc-daygrid-dot-event-contrast: color-mix(in srgb, var(--calendar-fc-daygrid-dot-event-background) 70%, black);
    }

    .star i {
        color: #ccc;
        font-size: 24px;
        cursor: pointer;
    }

    .star i.active {
        color: gold;
    }

    .feedback-block {
        border: 1px solid #5badff28;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        background-color: #dee0e231
    }

    .star-rating .fa-star {
        font-size: 1.5rem;
        cursor: pointer;
        color: #ccc;
    }

    .star-rating .selected {
        color: #ffc107;
    }

    .btn-group-toggle label {
        cursor: pointer;
    }

    #questions-scrollable {
        max-height: 55vh;
        overflow-y: auto;
        margin-bottom: 1.5rem;
        padding-right: 10px;
        padding-left: 10px;
        border: 1px solid rgba(238, 238, 238, 0.164);
        border-radius: 8px;
        background: inherit;
        scroll-behavior: smooth;
    }

    /* Chrome, Edge, Safari */
    #questions-scrollable::-webkit-scrollbar {
        width: 10px;
        background: #f1f1f1;
        border-radius: 8px;
    }

    #questions-scrollable::-webkit-scrollbar-thumb {
        background: #b3b3b3;
        border-radius: 8px;
        border: 2px solid #fafbfc;
        transition: background 0.3s;
    }

    #questions-scrollable::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    /* Firefox */
    #questions-scrollable {
        scrollbar-width: thin;
        scrollbar-color: #b3b3b3 #f1f1f1;
    }

    button.feedback-mark-button {
        /* background-color: inherit; */
        /* color: inherit; */
    }

    .score-badge {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #068a48c4;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.3rem;
        border: 2px solid currentColor;
        /* just to have a visible outline */
    }

    .feedback-block {
        padding: 1rem;
        position: relative;
        /* opacity: 0.2; */
        transform: translateY(20px);
        transition: all 0.4s ease;
        border-radius: 6px;
        background-color: #cbd3da31
    }

    .feedback-block:hover {
        background-color: #4b8a6a3a;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .feedback-stars i {
        font-size: 1.5rem;
        color: gold;
        /* You can override this in your theme */
        margin-right: 3px;
    }

    .action-area {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-icon {
        padding: 4px 7px;
        font-size: 0.9rem;
    }

    .pagination .page-link {
        /* background-color removed */
        border: none;
        margin: 0 2px;
        color: black;
    }

    .pagination .page-item.active .page-link {
        /* background-color removed */
        border-color: transparent;
        color: inherit;
    }

    .pagination .page-link:hover {
        background-color: #044a7942
    }

    .pagination .page-item.active {
        color: white;
    }

    /* Search input group styling */
    .input-group-text {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        border-color: #ccc;
        cursor: pointer;
        background-color: transparent;
        color: inherit;
    }

    .form-control {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
        border-color: #ccc;
        background-color: transparent;
        color: inherit;
    }

    .form-control::placeholder {
        color: #999;
    }

    .form-control:focus {
        border-color: #28a745;
        /* You can override this */
        box-shadow: none;
        background-color: transparent;
        color: inherit;
    }

    .input-group-text:focus {
        border-color: #28a745;
        box-shadow: none;
    }

    #comment {
        background-color: inherit;
        color: inherit;
    }

    #itemsPerPage {
        color: #1b1b1b;
    }

    .feedback-mark-button {
        border-radius: 30px;
        min-width: 40px;
        height: 40px;
        font-weight: bold;
        transition: all 0.2s ease;
        border: 2px solid #17a2b8;
    }

    .feedback-mark-button:hover {
        background-color: #17a2b8;
        color: white;
    }

    .feedback-mark-button.active {
        background-color: #17a2b8;
        color: white;
        box-shadow: 0 0 8px rgba(23, 162, 184, 0.6);
    }

    .feedback-stars.editable i {
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .feedback-stars.editable i.active,
    .feedback-stars.editable i:hover,
    .feedback-stars.editable i.fas,
    .feedback-stars.editable i.hovered {
        color: #ffc107 !important;
        transform: scale(1.1);
    }

    .feedback-stars.editable {
        user-select: none;
    }

    select.form-control {
        max-width: 200px;
        border-radius: 0.5rem;
    }
    .customer-success-block {
        background-color: #8b8b8b17 !important;
    }
    .customer-success-block i.fas {
        font-size: 1.8rem;
    }

    .small-star {
        font-size: 0.8rem !important;
    }

    .h-scroll-wrapper {
    overflow-x: auto;
    white-space: nowrap;
  }

  .feedback-block {
    display: block;
    min-width: 350px;
    max-width: 100%;
    vertical-align: top;
    white-space: normal;
    border-right: 1px solid #ddd;
    padding-right: 10px;
    margin-right: 10px;
  }

  .small-stars.feedback-stars i {
    font-size: 14px;
  }

  /* Optional: Hide default scrollbar in Chrome */
  .h-scroll-wrapper::-webkit-scrollbar {
    height: 6px;
  }

  .h-scroll-wrapper::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
  }

  .feedback-scroll-area {
    max-height: 400px; /* adjust height as needed */
    overflow-y: auto;
    padding-right: 8px; /* space for scrollbar */
  }

  .feedback-scroll-area::-webkit-scrollbar {
    width: 6px;
  }

  .feedback-scroll-area::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
  }
</style>
