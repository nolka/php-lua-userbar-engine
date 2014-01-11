local base = _G

module("Session")

local function checkState()
    if base.session_status() ~= 2 then
        start()
    end
end

function start()
    return base.session_start()
end

function destroy()
    return base.session_destroy()
end

function set(var, value)
    checkState()
    base.set_session_var(var, value)
end

function get(name)
    checkState()
    return base.get_session_var(name)
end