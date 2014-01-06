local base = _G

module("Session")

function start()
    return base.session_start()
end

function destroy()
    return base.session_destroy()
end

function set(var, value)
    base.set_session_var(var, value)
end

function get(name)
    return base.get_session_var(name)
end