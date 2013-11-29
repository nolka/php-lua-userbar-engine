local base = _G

module('Base')
mtab = { __index = _M }

local Factory = base.require('Factory')

function new()
  return Factory.create(_M)
end

function construct(self)
end
