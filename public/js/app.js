$(document).ready(function() {

    let crudSyncClientId = localStorage.getItem('tmbs_crud_sync_client_id');

    if (!crudSyncClientId) {
        crudSyncClientId = Date.now() + '-' + Math.random().toString(36).substring(2);
        localStorage.setItem('tmbs_crud_sync_client_id', crudSyncClientId);
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-CRUD-SYNC-ID': crudSyncClientId,
            'Accept': 'application/json'
        }
    });

    function clearErrors() {
        $('.error-message').remove();
        $('.is-invalid').removeClass('is-invalid');
        $('.input-error').removeClass('input-error');
    }

    function showErrors(errors) {
        clearErrors();

        $.each(errors, function(field, messages) {
            let input = $('[name="' + field + '"]');
            input.addClass('is-invalid input-error');
            input.closest('.form-group').append('<div class="error-message">' + messages[0] + '</div>');
        });
    }

    function showMessage(message) {
        let box = $('#ajaxMessage');

        if (!box.length) {
            $('body').append(
                '<div id="ajaxMessage" class="alert-success-pop" style="display:none;position:fixed;top:18px;left:50%;transform:translateX(-50%);z-index:9999;max-width:min(520px, calc(100vw - 32px));background:#16a34a;color:white;font-weight:600;padding:14px 22px;border-radius:12px;box-shadow:0 12px 30px rgba(22, 163, 74, 0.35);text-align:center;"></div>'
            );
            box = $('#ajaxMessage');
        }

        box.html('<i class="fas fa-check-circle"></i> ' + message).stop(true, true).fadeIn(150);

        setTimeout(function() {
            box.fadeOut(300);
        }, 3000);
    }

    function showErrorMessage(message) {
        let box = $('#ajaxErrorMessage');

        if (!box.length) {
            $('body').append(
                '<div id="ajaxErrorMessage" class="alert-error-pop" style="display:none;position:fixed;top:18px;left:50%;transform:translateX(-50%);z-index:10000;max-width:min(560px, calc(100vw - 32px));background:#dc2626;color:white;font-weight:600;padding:14px 22px;border-radius:12px;box-shadow:0 12px 30px rgba(220, 38, 38, 0.35);text-align:center;"></div>'
            );
            box = $('#ajaxErrorMessage');
        }

        box.html('<i class="fas fa-exclamation-circle"></i> ' + message).stop(true, true).fadeIn(150);

        setTimeout(function() {
            box.fadeOut(300);
        }, 4000);
    }

    window.showAjaxMessage = showMessage;
    window.showAjaxErrorMessage = showErrorMessage;

    function getAjaxErrorMessage(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            return Object.values(xhr.responseJSON.errors).flat()[0] || 'Please check the form fields.';
        }

        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        return 'Something went wrong. Please try again.';
    }

    let crudRouteAliases = {
        students: ['students', 'student'],
        teachers: ['teachers', 'teacher'],
        degrees: ['degrees', 'degree'],
        departments: ['departments', 'department']
    };

    let crudListPaths = {
        students: '/student',
        teachers: '/teacher',
        degrees: '/degrees',
        departments: '/departments'
    };

    function resourceFromUrl(url) {
        let match = url.match(/^\/(students|student|teachers|teacher|degrees|degree|departments|department)(?:\/|$)/);

        if (!match) {
            return null;
        }

        let route = match[1];

        if (route === 'student') {
            return 'students';
        }

        if (route === 'teacher') {
            return 'teachers';
        }

        if (route === 'degree') {
            return 'degrees';
        }

        if (route === 'department') {
            return 'departments';
        }

        return route;
    }

    function resourceLabel(resource) {
        return {
            students: 'Student',
            teachers: 'Teacher',
            degrees: 'Degree',
            departments: 'Department'
        }[resource] || 'Record';
    }

    function shouldRefreshForCrud(resource) {
        let path = window.location.pathname;
        let aliases = crudRouteAliases[resource] || [resource];

        return aliases.some(function(alias) {
            return path === '/' + alias || path.indexOf('/' + alias + '/') === 0;
        });
    }

    function currentCrudPath(resource) {
        let path = window.location.pathname;
        let aliases = crudRouteAliases[resource] || [resource];
        let currentAlias = aliases.find(function(alias) {
            return path === '/' + alias || path.indexOf('/' + alias + '/') === 0;
        });

        return '/' + (currentAlias || aliases[0]);
    }

    function firstListPath(resource) {
        return crudListPaths[resource] || currentCrudPath(resource);
    }

    function pageResourceId() {
        let parts = window.location.pathname.split('/').filter(Boolean);
        return parts.length > 1 ? parts[1] : null;
    }

    function syncMessage(change) {
        return resourceLabel(change.resource) + ' ' + change.action + ' in another window. List updated.';
    }

    function buildChange(resource, detail) {
        return {
            resource: resource,
            action: detail.action || 'changed',
            id: detail.id || null,
            origin: detail.origin || null,
            version: detail.version || 0
        };
    }

    function refreshForCrudChange(change) {
        if (!change.resource || !shouldRefreshForCrud(change.resource)) {
            return;
        }

        let currentPath = window.location.pathname;
        let targetPath = currentCrudPath(change.resource);

        if (currentPath === targetPath) {
            refreshContent(targetPath, syncMessage(change), false);
            return;
        }

        refreshContent(targetPath, syncMessage(change), true);
    }

    function notifyCrudChange(resource, action, id) {
        if (!resource) {
            return;
        }

        localStorage.setItem('tmbs_crud_sync', JSON.stringify({
            resource: resource,
            action: action,
            id: id || null,
            time: Date.now()
        }));
    }

    window.notifyCrudChange = notifyCrudChange;

    $(window).on('storage', function(e) {
        if (e.originalEvent.key !== 'tmbs_crud_sync' || !e.originalEvent.newValue) {
            return;
        }

        let change;

        try {
            change = JSON.parse(e.originalEvent.newValue);
        } catch (error) {
            return;
        }

        refreshForCrudChange(change);
    });

    let knownCrudVersions = {};
    let crudSyncReady = false;

    function checkCrudSync() {
        $.ajax({
            url: '/crud-sync',
            method: 'GET',
            dataType: 'json',
            cache: false,
            success: function(response) {
                let versions = response.versions || {};

                $.each(versions, function(resource, detail) {
                    let lastVersion = knownCrudVersions[resource] || null;
                    let currentVersion = detail.version || null;

                    knownCrudVersions[resource] = currentVersion;

                    if (!crudSyncReady || !lastVersion || currentVersion === lastVersion) {
                        return;
                    }

                    refreshForCrudChange(buildChange(resource, detail));
                });

                crudSyncReady = true;
            }
        });
    }

    checkCrudSync();
    setInterval(checkCrudSync, 1000);

    function resetFields(fields) {
        fields.forEach(function(selector) {
            $(selector).val('');
        });
    }

    function refreshContent(url, message, updateHistory) {
        let scrollTop = $(window).scrollTop();

        $.ajax({
            url: url,
            method: 'GET',
            data: {
                full_page: 1,
                _: Date.now()
            },
            dataType: 'html',
            success: function(html) {
                let page = $('<div>').append($.parseHTML(html, document, true));
                let content = page.find('.content-container').html() || page.find('.content .container').html();
                let title = page.find('title').text();

                if (!content) {
                    return;
                }

                $('.content-container').html(content);
                document.title = title;

                if (updateHistory) {
                    history.pushState(null, '', url);
                }

                $(window).scrollTop(scrollTop);

                if (message) {
                    showMessage(message);
                }
            }
        });
    }

    let studentAutoRefreshTimer = null;

    function loadStudents(message) {
        refreshContent(currentCrudPath('students'), message || null, false);
    }

    function startStudentAutoRefresh(intervalMs) {
        if (studentAutoRefreshTimer) {
            clearInterval(studentAutoRefreshTimer);
        }

        studentAutoRefreshTimer = setInterval(function() {
            if (shouldRefreshForCrud('students')) {
                loadStudents();
            }
        }, intervalMs || 3000);
    }

    window.loadStudents = loadStudents;
    window.startStudentAutoRefresh = startStudentAutoRefresh;

    function ajaxGoTo(url, message) {
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                full_page: 1
            },
            dataType: 'html',
            success: function(html) {
                let page = $('<div>').append($.parseHTML(html, document, true));
                let content = page.find('.content-container').html() || page.find('.content .container').html();
                let title = page.find('title').text();

                if (content) {
                    $('.content-container').html(content);
                    document.title = title;
                    history.pushState(null, '', url);
                    if (message) {
                        showMessage(message);
                    }
                    return;
                }

                window.location.href = url;
            },
            error: function() {
                window.location.href = url;
            }
        });
    }

    $(document).on('click', '.ajax-page-link', function(e) {
        e.preventDefault();
        ajaxGoTo($(this).attr('href'));
    });

    $(document).on('submit', '#addStudentForm, #editStudentForm, #addTeacherForm, #editTeacherForm, #addDegreeForm, #editDegreeForm', function(e) {
        e.preventDefault();

        let button = $(this).find('button[id]').first();

        if (button.length) {
            button.trigger('click');
        }
    });

    $(document).on('click', '#updateStudentBtn', function(e) {
        e.preventDefault();
        clearErrors();

        let studentId = $('#studentId').val() || pageResourceId();

        $.ajax({
            url: '/students/' + studentId,
            method: 'PUT',
            data: {
                fname: $('#fname').val(),
                mname: $('#mname').val(),
                lname: $('#lname').val(),
                email: $('#email').val(),
                contactno: $('#contactno').val(),
                degree_id: $('#degree_id').val()
            },
            success: function() {
                notifyCrudChange('students', 'updated', studentId);
                ajaxGoTo(firstListPath('students'), 'Student updated successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                    showErrorMessage(getAjaxErrorMessage(xhr));
                    return;
                }

                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

    $(document).on('click', '#updateTeacherBtn', function(e) {
        e.preventDefault();
        clearErrors();

        let teacherId = $('#teacherId').val() || pageResourceId();

        $.ajax({
            url: '/teachers/' + teacherId,
            method: 'PUT',
            data: {
                fname: $('#fname').val(),
                mname: $('#mname').val(),
                lname: $('#lname').val(),
                email: $('#email').val(),
                contactno: $('#contactno').val(),
                department_id: $('#department_id').val(),
                username: $('#username').val(),
                password: $('#password').val()
            },
            success: function() {
                notifyCrudChange('teachers', 'updated', teacherId);
                ajaxGoTo(firstListPath('teachers'), 'Teacher updated successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                    showErrorMessage(getAjaxErrorMessage(xhr));
                    return;
                }

                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

    $(document).on('click', '#updateDegreeBtn', function(e) {
        e.preventDefault();
        clearErrors();

        let degreeId = $('#degreeId').val() || pageResourceId();

        $.ajax({
            url: '/degrees/' + degreeId,
            method: 'PUT',
            data: {
                degree_title: $('#edit_degree_title').val()
            },
            success: function() {
                notifyCrudChange('degrees', 'updated', degreeId);
                ajaxGoTo(firstListPath('degrees'), 'Degree updated successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                    showErrorMessage(getAjaxErrorMessage(xhr));
                    return;
                }

                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

    $(document).on('click', '#updateDepartmentBtn', function(e) {
        e.preventDefault();
        clearErrors();

        $.ajax({
            url: '/departments/' + $('#department_id').val(),
            method: 'PUT',
            data: {
                department_name: $('#edit_department_name').val()
            },
            success: function() {
                notifyCrudChange('departments', 'updated', $('#department_id').val());
                ajaxGoTo('/departments', 'Department updated successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                    showErrorMessage(getAjaxErrorMessage(xhr));
                    return;
                }

                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

    $(document).on('click', '#saveStudent', function(e) {
        e.preventDefault();
        clearErrors();

        let formData = new FormData($('#addStudentForm')[0]);

        $.ajax({
            url: '/students',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                notifyCrudChange('students', 'added');
                ajaxGoTo(firstListPath('students'), 'Student added successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                    showErrorMessage(getAjaxErrorMessage(xhr));
                    return;
                }

                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

    $(document).on('click', '#saveTeacher', function(e) {
        e.preventDefault();
        clearErrors();

        $.ajax({
            url: '/teachers',
            method: 'POST',
            data: {
                fname: $('#teacher_fname').val(),
                mname: $('#teacher_mname').val(),
                lname: $('#teacher_lname').val(),
                email: $('#teacher_email').val(),
                contactno: $('#teacher_contactno').val(),
                department_id: $('#teacher_department_id').val(),
                username: $('#teacher_username').val(),
                password: $('#teacher_password').val()
            },
            success: function() {
                notifyCrudChange('teachers', 'added');
                ajaxGoTo(firstListPath('teachers'), 'Teacher added successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                    showErrorMessage(getAjaxErrorMessage(xhr));
                    return;
                }

                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

    $(document).on('click', '#saveDegree', function(e) {
        e.preventDefault();
        clearErrors();

        $.ajax({
            url: '/degrees',
            method: 'POST',
            data: {
                degree_title: $('#degree_title').val()
            },
            success: function() {
                notifyCrudChange('degrees', 'added');
                ajaxGoTo(firstListPath('degrees'), 'Degree added successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                    showErrorMessage(getAjaxErrorMessage(xhr));
                    return;
                }

                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

    $(document).on('click', '#saveDepartment', function(e) {
        e.preventDefault();
        clearErrors();

        $.ajax({
            url: '/departments',
            method: 'POST',
            data: {
                department_name: $('#department_name').val()
            },
            success: function() {
                notifyCrudChange('departments', 'added');
                ajaxGoTo('/departments', 'Department added successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                    showErrorMessage(getAjaxErrorMessage(xhr));
                    return;
                }

                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

    $(document).on('submit', '.action-form', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        let form = $(this);

        $.ajax({
            url: form.attr('action'),
            method: 'DELETE',
            success: function() {
                let message = 'Record deleted successfully!';
                let resource = resourceFromUrl(form.attr('action'));

                if (form.attr('action').indexOf('/teachers/') === 0) {
                    message = 'Teacher deleted successfully!';
                } else if (form.attr('action').indexOf('/degrees/') === 0) {
                    message = 'Degree deleted successfully!';
                } else if (form.attr('action').indexOf('/students/') === 0) {
                    message = 'Student deleted successfully!';
                } else if (form.attr('action').indexOf('/departments/') === 0) {
                    message = 'Department deleted successfully!';
                }

                showMessage(message);
                form.closest('tr').remove();
                notifyCrudChange(resource, 'deleted');
            },
            error: function(xhr) {
                showErrorMessage(getAjaxErrorMessage(xhr));
            }
        });
    });

});

function getGlobalAjaxErrorMessage(xhr) {
    if (xhr.responseJSON && xhr.responseJSON.errors) {
        return Object.values(xhr.responseJSON.errors).flat()[0] || 'Please check the form fields.';
    }

    if (xhr.responseJSON && xhr.responseJSON.message) {
        return xhr.responseJSON.message;
    }

    return 'Something went wrong. Please try again.';
}

function viewStudent(id) {
    $.get('/students/' + id, function(student) {
        let panel = $('<div id="studentDetail" class="department-detail-panel" style="display:none;margin-bottom:20px;padding:18px;border-radius:14px;background:#eff6ff;color:#1e293b;border:1px solid #bfdbfe;"></div>');

        if (!$('#studentDetail').length) {
            $('.student-card .table-responsive').before(panel);
        }

        $('#studentDetail').html(
            '<strong>Student:</strong> ' + student.fname + ' ' + (student.mname || '') + ' ' + student.lname +
            '<br><strong>Email:</strong> ' + ((student.user_account && student.user_account.email) || student.email || 'No email') +
            '<br><strong>Contact:</strong> ' + (student.contactno || 'No contact') +
            '<br><strong>Degree:</strong> ' + ((student.degree && student.degree.degree_title) || 'No Degree')
        ).slideDown();
    });
}

function viewTeacher(id) {
    $.get('/teachers/' + id, function(teacher) {
        let panel = $('<div id="teacherDetail" class="department-detail-panel" style="display:none;margin-bottom:20px;padding:18px;border-radius:14px;background:#eff6ff;color:#1e293b;border:1px solid #bfdbfe;"></div>');

        if (!$('#teacherDetail').length) {
            $('.teacher-card .table-responsive').before(panel);
        }

        $('#teacherDetail').html(
            '<strong>Teacher:</strong> ' + teacher.fname + ' ' + (teacher.mname || '') + ' ' + teacher.lname +
            '<br><strong>Email:</strong> ' + ((teacher.user_account && teacher.user_account.email) || teacher.email || 'No email') +
            '<br><strong>Contact:</strong> ' + (teacher.contactno || 'No contact') +
            '<br><strong>Department:</strong> ' + ((teacher.department && teacher.department.department_name) || 'No department')
        ).slideDown();
    });
}

function viewDegree(id) {
    $.get('/degrees/' + id, function(degree) {
        let panel = $('<div id="degreeDetail" class="department-detail-panel" style="display:none;margin-bottom:20px;padding:18px;border-radius:14px;background:#eff6ff;color:#1e293b;border:1px solid #bfdbfe;"></div>');

        if (!$('#degreeDetail').length) {
            $('.degree-card .table-wrapper').before(panel);
        }

        $('#degreeDetail').html(
            '<strong>ID:</strong> ' + degree.id +
            '<br><strong>Degree Title:</strong> ' + degree.degree_title
        ).slideDown();
    });
}

function viewDepartment(id) {
    $.get('/departments/' + id, function(department) {
        $('#departmentDetail').html(
            '<strong>ID:</strong> ' + department.id +
            '<br><strong>Department Name:</strong> ' + department.department_name
        ).slideDown();
    });
}

function deleteStudent(id, button) {
    if (!confirm('Are you sure you want to delete this student?')) {
        return;
    }

    $.ajax({
        url: '/students/' + id,
        method: 'DELETE',
        success: function() {
            $(button).closest('tr').remove();
            if (window.showAjaxMessage) {
                window.showAjaxMessage('Student deleted successfully!');
            }
            if (window.notifyCrudChange) {
                window.notifyCrudChange('students', 'deleted', id);
            }
        },
        error: function(xhr) {
            if (window.showAjaxErrorMessage) {
                window.showAjaxErrorMessage(getGlobalAjaxErrorMessage(xhr));
            }
        }
    });
}

function deleteTeacher(id, button) {
    if (!confirm('Are you sure you want to delete this teacher?')) {
        return;
    }

    $.ajax({
        url: '/teachers/' + id,
        method: 'DELETE',
        success: function() {
            $(button).closest('tr').remove();
            if (window.showAjaxMessage) {
                window.showAjaxMessage('Teacher deleted successfully!');
            }
            if (window.notifyCrudChange) {
                window.notifyCrudChange('teachers', 'deleted', id);
            }
        },
        error: function(xhr) {
            if (window.showAjaxErrorMessage) {
                window.showAjaxErrorMessage(getGlobalAjaxErrorMessage(xhr));
            }
        }
    });
}

function deleteDegree(id, button) {
    if (!confirm('Are you sure you want to delete this degree?')) {
        return;
    }

    $.ajax({
        url: '/degrees/' + id,
        method: 'DELETE',
        success: function() {
            $(button).closest('tr').remove();
            if (window.showAjaxMessage) {
                window.showAjaxMessage('Degree deleted successfully!');
            }
            if (window.notifyCrudChange) {
                window.notifyCrudChange('degrees', 'deleted', id);
            }
        },
        error: function(xhr) {
            if (window.showAjaxErrorMessage) {
                window.showAjaxErrorMessage(getGlobalAjaxErrorMessage(xhr));
            }
        }
    });
}

function deleteDepartment(id, button) {
    if (!confirm('Are you sure you want to delete this department?')) {
        return;
    }

    $.ajax({
        url: '/departments/' + id,
        method: 'DELETE',
        success: function() {
            $(button).closest('tr').remove();
            if (window.showAjaxMessage) {
                window.showAjaxMessage('Department deleted successfully!');
            }
            if (window.notifyCrudChange) {
                window.notifyCrudChange('departments', 'deleted', id);
            }
        },
        error: function(xhr) {
            if (window.showAjaxErrorMessage) {
                window.showAjaxErrorMessage(getGlobalAjaxErrorMessage(xhr));
            }
        }
    });
}

$(document).ready(function () {
    var $page = $('#userAccountsPage');

    if (!$page.length) {
        return;
    }

    var usersUrl = $page.data('users-url');
    var $form = $('#userForm');
    var $tableBody = $('#userTableBody');
    var $message = $('#userAjaxMessage');
    var $error = $('#userAjaxError');
    var $password = $('#password');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showMessage(text) {
        $error.hide().text('');
        $message.text(text).fadeIn();
    }

    function showError(text) {
        $message.hide().text('');
        $error.text(text).fadeIn();
    }

    function getErrorMessage(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            return Object.values(xhr.responseJSON.errors).flat().join(' ');
        }

        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        return 'Something went wrong. Please try again.';
    }

    function resetForm() {
        $form[0].reset();
        $('#user_id').val('');
        $('#role').val('student');
        $('#is_active').prop('checked', true);
        $password.attr('required', true).attr('placeholder', '');
        $('#saveUserButton').text('Save User');
        $('#cancelUserEdit').hide();
    }

    function cellText(text) {
        return $('<td>').text(text || 'N/A');
    }

    function actionButton(className, title, svg) {
        return $('<button>', {
            type: 'button',
            class: 'icon-btn ' + className,
            title: title,
            'aria-label': title
        }).html(svg);
    }

    function renderUsers(users) {
        $tableBody.empty();

        if (!users.length) {
            $tableBody.append(
                $('<tr>').append(
                    $('<td>', {
                        colspan: 5,
                        text: 'No user accounts found.'
                    })
                )
            );
            return;
        }

        users.forEach(function (user) {
            var editSvg = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path></svg>';
            var deleteSvg = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path></svg>';
            var $actions = $('<div>', { class: 'icon-actions' });
            var $edit = actionButton('icon-edit edit-user', 'Edit User', editSvg).data('user', user);
            var $delete = actionButton('icon-delete delete-user', 'Delete User', deleteSvg).data('id', user.id);

            $actions.append($edit, $delete);

            $tableBody.append(
                $('<tr>', { 'data-id': user.id }).append(
                    cellText(user.username),
                    cellText(user.email),
                    cellText(user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : ''),
                    cellText(user.is_active ? 'Active' : 'Inactive'),
                    $('<td>').append($actions)
                )
            );
        });
    }

    function loadUsers() {
        $.ajax({
            url: usersUrl,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                renderUsers(response.users || []);
            },
            error: function (xhr) {
                showError(getErrorMessage(xhr));
            }
        });
    }

    $form.on('submit', function (event) {
        event.preventDefault();

        var userId = $('#user_id').val();
        var method = userId ? 'PUT' : 'POST';
        var url = userId ? usersUrl + '/' + userId : usersUrl;
        var formData = $form.serialize();

        $.ajax({
            url: url,
            method: method,
            data: formData,
            dataType: 'json',
            success: function (response) {
                showMessage(response.message);
                resetForm();
                loadUsers();
            },
            error: function (xhr) {
                showError(getErrorMessage(xhr));
            }
        });
    });

    $tableBody.on('click', '.edit-user', function () {
        var user = $(this).data('user');

        $('#user_id').val(user.id);
        $('#username').val(user.username);
        $('#email').val(user.email);
        $('#role').val(user.role);
        $('#is_active').prop('checked', Boolean(user.is_active));
        $password.val('').removeAttr('required').attr('placeholder', 'Leave blank to keep current password');
        $('#saveUserButton').text('Update User');
        $('#cancelUserEdit').show();
        $('html, body').animate({ scrollTop: $form.offset().top - 20 }, 250);
    });

    $tableBody.on('click', '.delete-user', function () {
        var userId = $(this).data('id');

        if (!confirm('Delete this user account?')) {
            return;
        }

        $.ajax({
            url: usersUrl + '/' + userId,
            method: 'DELETE',
            dataType: 'json',
            success: function (response) {
                showMessage(response.message);
                loadUsers();
            },
            error: function (xhr) {
                showError(getErrorMessage(xhr));
            }
        });
    });

    $('#resetUserForm, #cancelUserEdit').on('click', function () {
        resetForm();
        $message.hide().text('');
        $error.hide().text('');
    });

    resetForm();
    loadUsers();
});
